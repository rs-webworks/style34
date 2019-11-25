<?php declare(strict_types=1);

namespace EryseClient\Model\Server\UserRole\Service;

use EryseClient\Model\Server\UserRole\Entity\UserRole;

/**
 * Class UserRoleService
 * @package EryseClient\Model\Server\UserRole\Service
 */
class UserRoleService
{
    private const BLOCKED_ROLES = [
        UserRole::BANNED,
        UserRole::DELETED
    ];

    /**
     * @param string $role
     * @return bool
     */
    public function isRoleBlocked(string $role): bool
    {
        return in_array($role, self::BLOCKED_ROLES);
    }

}

