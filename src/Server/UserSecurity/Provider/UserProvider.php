<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Provider;

use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use Exception;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package EryseClient\Client\ProfileSecurity\Provider
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserProvider constructor.
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
     * @return UserInterface
     * @throws Exception
     */
    public function loadUserByUsername($username)
    {
        return $this->userService->initUser($this->userRepository->loadUserByUsername($username));
    }

    /**
     * @param UserInterface $user
     * @return User|UserInterface|null
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->userService->initUser($this->userRepository->find($user->getId()));
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
