<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Authenticator;

use EryseClient\Client\ProfileSecurity\Exception\LoginException;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\UserRole\Service\UserRoleService;
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
 *
 * @package EryseClient\Security
 */
class UserLoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;
    use TranslatorAwareTrait;

    public const ROUTE = "user-security-login";
    public const METHOD = "POST";

    /** Credentials */
    public const USER_AUTH = 'user_auth';
    public const USER_PASSWORD = 'user_password';
    public const TFA_CODE = 'tfa_code';
    public const TFA_METHOD = 'tfa_method';
    public const CSRF_TOKEN = 'csrf_token';

    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var RouterInterface */
    private $router;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var SessionInterface */
    private $session;

    /** @var UserRoleService */
    private $userRoleService;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param UserRoleService $userRoleService
     * @param RouterInterface $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     */
    public function __construct(
        UserRoleService $userRoleService,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        SessionInterface $session
    ) {
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->userRoleService = $userRoleService;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === self::ROUTE && $request->isMethod(self::METHOD);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            self::USER_AUTH => $request->request->get(self::USER_AUTH),
            self::USER_PASSWORD => $request->request->get(self::USER_PASSWORD),
            self::TFA_CODE => $request->request->get(self::TFA_CODE),
            self::TFA_METHOD => $request->request->get(self::TFA_METHOD),
            self::CSRF_TOKEN => $request->request->get(self::CSRF_TOKEN),
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['auth']);

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
        $token = new CsrfToken('authenticate', $credentials[self::CSRF_TOKEN]);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        /** @var User $user */
        $user = $userProvider->loadUserByUsername($credentials[self::USER_AUTH]);

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
        return $this->passwordEncoder->isPasswordValid($user, $credentials[self::USER_PASSWORD]);
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
        return $this->router->generate('security-login');
    }

}
