<?php declare(strict_types=1);

namespace EryseClient\EventListener;

use EryseClient\Service\ProfileService;
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
}
