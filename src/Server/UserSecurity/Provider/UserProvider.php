<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Provider;

use Doctrine\ORM\ORMException;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 *
 * @package EryseClient\Server\UserSecurity\Provider
 */
class UserProvider implements UserProviderInterface
{
    use LoggerAwareTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /** @var UserService */
    private $userService;

    /**
     * UserProvider constructor.
     *
     * @param UserRepository $userRepository
     * @param UserService $userService
     */
    public function __construct(
        UserRepository $userRepository,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }


    /**
     * @param string $username
     *
     * @return UserInterface|void
     */
    public function loadUserByUsername($username)
    {
        try {
            $user = $this->userRepository->loadUserByUsername($username);

            return $this->userService->initUser($user);
        } catch (ORMException $e) {
            $this->logger->info("Failed to provide user", [$e]);
        }
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface|void
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $user = $this->userRepository->find($user->getId());

        return $this->userService->initUser($user);
    }

    /**
     * @param string $class
     *
     * @return bool|void
     */
    public function supportsClass($class)
    {
        if (User::class === $class) {
            return true;
        }
    }

}
