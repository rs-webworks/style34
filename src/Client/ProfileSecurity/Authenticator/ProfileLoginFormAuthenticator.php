<?php declare(strict_types=1);

namespace EryseClient\Client\ProfileSecurity\Authenticator;

use Doctrine\ORM\NonUniqueResultException;
use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Client\Profile\Entity\Role as ProfileRole;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\ProfileSecurity\Exception\LoginException;
use EryseClient\Common\Utility\TranslatorTrait;
use EryseClient\Server\UserRole\Entity\UserRole as UserRole;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator
 * @package EryseClient\Security
 */
class ProfileLoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;
    use TranslatorTrait;

    /** @var ProfileRepository */
    private $profileRepository;

    /** @var RouterInterface */
    private $router;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var SessionInterface */
    private $session;

    /**
     * LoginFormAuthenticator constructor.
     * @param RouterInterface $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ProfileRepository $profileRepository
     * @param SessionInterface $session
     */
    public function __construct(
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        ProfileRepository $profileRepository,
        SessionInterface $session
    ) {
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->profileRepository = $profileRepository;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'security-login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'auth' => $request->request->get('_auth'),
            'password' => $request->request->get('_password'),
            'code' => $request->request->get('_code'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()
            ->set(
                Security::LAST_USERNAME,
                $credentials['auth']
            );

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $profileProvider
     * @return object|UserInterface|null
     * @throws LoginException
     * @throws NonUniqueResultException
     */
    public function getUser($credentials, UserProviderInterface $profileProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        /** @var Profile $profile */
        $profile = $this->profileRepository->loadUserByUsername($credentials['auth']);

        if (!$profile) {
            throw new LoginException($this->translator->trans('login-failed', [], 'security'));
        }

        $userBlocked = in_array(
            $profile->getUser()
                ->getRole(),
            [UserRole::BANNED, UserRole::INACTIVE]
        );

        $profileBlocked = in_array(
            $profile->getRole(),
            [ProfileRole::BANNED, ProfileRole::INACTIVE, ProfileRole::DELETED,]
        );

        if ($userBlocked || $profileBlocked) {
            throw new LoginException($this->translator->trans('login-not-allowed', [], 'security'));
        }

        return $profile;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $profile
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $profile)
    {
        return $this->passwordEncoder->isPasswordValid($profile, $credentials['password']);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse|Response|null
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('home-index'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('security-login');
    }
}
