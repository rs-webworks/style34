<?php

namespace EryseClient\EventListener;

use EryseClient\Entity\Profile\Profile;
use EryseClient\Entity\Profile\Role;
use EryseClient\Service\ProfileService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class KernelRequestListener
 * @package EryseClient\EventListener
 */
class KernelRequestListener
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var ProfileService
     */
    private $profileService;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * KernelRequestListener constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     * @param ProfileService $profileService
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        ProfileService $profileService,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->profileService = $profileService;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // Invalidate user is there is remember-me token missing in database
        /** @var Profile $user */
//        $token = $this->tokenStorage->getToken();
//        $user = $token ? $token->getUser() : null;
//
//        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
//
//            if (!$this->profileService->hasRememberMeToken($user)) {
//                $url = $this->router->generate('profile-logout');
//                $response = new RedirectResponse($url);
//                $event->setResponse($response);
//            }
//        }
    }

}