<?php declare(strict_types=1);

namespace EryseClient\Server\User\EventListener;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Server\User\Event\BeforeRegistrationEvent;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BeforeRegistrationSubscriber
 */
class PurgeRegistrationRequestsSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserService
     */
    private UserService $userService;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * PurgeRegistrationRequestsSubscriber constructor.
     *
     * @param UserService $userService
     * @param UserRepository $userRepository
     */
    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * @return array|void
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeRegistrationEvent::class => 'purgeRegistrationRequests'
        ];
    }

    /**
     * @param BeforeRegistrationEvent $event
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function purgeRegistrationRequests(BeforeRegistrationEvent $event): void
    {
        $expiredRegistrations = $this->userService->findExpiredRegistrations();
        if ($expiredRegistrations) {
            $this->userRepository->removeUsers($expiredRegistrations);
        }
    }

}
