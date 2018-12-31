<?php

namespace eRyseClient\Security;

use eRyseClient\Entity\Profile\Profile;
use eRyseClient\Exception\Security\LoginException;
use eRyseClient\Exception\Security\TwoStepAuthSetException;
use eRyseClient\Repository\Profile\ProfileRepository;
use eRyseClient\Traits\EntityManagerTrait;
use eRyseClient\Traits\TranslatorTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
 * @package eRyseClient\Security
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;
    use TranslatorTrait;
    use EntityManagerTrait;

    /** @var ProfileRepository $profileRepository */
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
        return 'profile-login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
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
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['auth']
        );

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return object|UserInterface|null
     * @throws LoginException
     * @throws TwoStepAuthSetException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        /** @var Profile $user */
        $user = $this->profileRepository->loadUserByUsername($credentials['auth']);

        if (!$user) {
            // fail authentication with a custom error
            throw new LoginException($this->translator->trans('login-failed', [], 'security'));
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('home-index'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('profile-login');
    }
}