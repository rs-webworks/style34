<?php declare(strict_types=1);

namespace EryseClient\Model\Server\UserRole\EntityListener;

use EryseClient\Entity\Client\User\Role;
use EryseClient\Model\Server\User\Repository\UserRepository;

/**
 * TODO: FIX refactor
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
