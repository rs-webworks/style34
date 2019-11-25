<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Provider;

use Doctrine\ORM\ORMException;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package EryseClient\Server\UserSecurity\Provider
 */
class UserProvider implements UserProviderInterface
{
    use LoggerAwareTrait;

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
     * @return UserInterface|void
     */
    public function loadUserByUsername($username)
    {
        try {
            return $this->userRepository->loadUserByUsername($username);
        } catch (ORMException $e) {
            $this->logger->info("Failed to provide user", [$e]);
        }
    }

    /**
     * @param UserInterface $user
     * @return UserInterface|void
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
     * @return bool|void
     */
    public function supportsClass($class)
    {
        if (User::class === $class) {
            return true;
        }
    }

}
