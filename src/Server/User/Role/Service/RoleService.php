<?php declare(strict_types=1);

namespace EryseClient\Server\User\Role\Service;

use EryseClient\Common\Service\AbstractService;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Role\Entity\RoleEntity;

/**
 * Class UserRoleService
 *
 */
class RoleService extends AbstractService
{
    private const BLOCKED_ROLES = [
        RoleEntity::BANNED,
        RoleEntity::DELETED
    ];

    /**
     * @param string $role
     * @return bool
     */
    public function isRoleBlocked(string $role): bool
    {
        return in_array($role, self::BLOCKED_ROLES);
    }

    /**
     * @param UserEntity $user
     *
     * @return bool
     */
    public function isRoleAdmin(UserEntity $user): bool
    {
        return $user->getRole() == RoleEntity::ADMIN;
    }
}

