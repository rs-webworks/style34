<?php declare(strict_types=1);

namespace EryseClient\EntityListener\Client\User;

use EryseClient\Entity\Client\User\Role;
use EryseClient\Repository\Server\User\UserRepository;

/**
 * Class RoleListener
 * @package EryseClient\EntityListener\Client\User
 */
class RoleListener
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * RoleListener constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Role $role
     */
    public function postLoad(Role $role)
    {
        $role->setUsers($this->userRepository->findByRole($role->getName()));
    }
}
