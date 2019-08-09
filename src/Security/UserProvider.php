<?php declare(strict_types=1);
namespace EryseClient\Security;

use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\Server\User\UserRepository;
use EryseClient\Utility\EntityManagersTrait;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package EryseClient\Security
 */
class UserProvider implements UserProviderInterface
{
    use EntityManagersTrait;
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * UserProvider constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     * @return UserInterface
     * @throws \Exception
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->loadUserByUsername($username) ?? false;
        if (!$user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->userRepository->find($user->getId());
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
