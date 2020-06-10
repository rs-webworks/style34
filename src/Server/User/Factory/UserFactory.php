<?php declare(strict_types=1);

namespace EryseClient\Server\User\Factory;

use DateTime;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Role\Entity\RoleEntity as UserRole;
use EryseClient\Server\User\Service\PasswordService;

/**
 * Class UserFactory
 */
class UserFactory
{
    /**
     * @var PasswordService
     */
    private PasswordService $passwordService;

    /**
     * UserFactory constructor.
     *
     * @param PasswordService $passwordService
     */
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $plainPassword
     *
     * @param string|null $lastIp
     *
     * @return UserEntity
     * @throws \Exception
     */
    public function createNewUser(
        string $username,
        string $email,
        string $plainPassword,
        ?string $lastIp = '0.0.0.0'
    ) : UserEntity {
        $user = new UserEntity();

        // Set basic properties
        $user->setUsername($username);
        $user->setEmail($email);

        // Get default user role
        $user->setRole(UserRole::INACTIVE);

        // Encode password
        $password = $this->passwordService->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        // Set defaults
        $user->setCreatedAt(new DateTime());
        $user->setLastIp($lastIp);
        $user->setRegisteredAs(serialize([$username, $email]));

        return $user;
    }
}
