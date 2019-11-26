<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Authenticator;

use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\ProfileSecurity\Exception\LoginException;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\UserRole\Service\UserRoleService;
use EryseClient\Server\UserSecurity\Form\Type\LoginType;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator
 *
 * @package EryseClient\Security
 */
class UserLoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;
    use TranslatorAwareTrait;

    public const ROUTE = "user-security-login";

    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var RouterInterface */
    private $router;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var SessionInterface */
    private $session;

    /** @var UserRoleService */
    private $userRoleService;

    /** @var ProfileRepository  */
    private $profileRepository;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param UserRoleService $userRoleService
     * @param ProfileRepository $profileRepository
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     */
    public function __construct(
        UserRoleService $userRoleService,
        ProfileRepository $profileRepository,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        SessionInterface $session
    ) {
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->userRoleService = $userRoleService;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === self::ROUTE && $request->isMethod(LoginType::METHOD);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $params = $request->request->get(LoginType::PREFIX);

        $credentials = [
            LoginType::USER_AUTH => $params[LoginType::USER_AUTH],
            LoginType::USER_PASSWORD => $params[LoginType::USER_PASSWORD],
//            LoginType::TFA_CODE => $params[LoginType::TFA_CODE],
//            LoginType::TFA_TYPE => $params[LoginType::TFA_TYPE],
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $credentials[LoginType::USER_AUTH]);

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return object|UserInterface|null
     * @throws LoginException
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = $userProvider->loadUserByUsername($credentials[LoginType::USER_AUTH]);

        if (!$user) {
            throw new LoginException($this->translator->trans('login-failed', [], 'security'));
        }

        if ($this->userRoleService->isRoleBlocked($user->getRole())) {
            throw new LoginException($this->translator->trans('login-not-allowed', [], 'security'));
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials[LoginType::USER_PASSWORD]);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
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
        return $this->router->generate(self::ROUTE);
    }

}
