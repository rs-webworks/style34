<?php declare(strict_types=1);

namespace EryseClient\Server\UserRole\Service;

use EryseClient\Common\Service\AbstractService;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\UserRole\Entity\UserRole;

/**
 * Class UserRoleService
 * @package EryseClient\Server\UserRole\Service
 */
class UserRoleService extends AbstractService
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

    /**
     * @param User $user
     * @return bool
     */
    public function isRoleAdmin(User $user): bool
    {
        return $user->getRole() == UserRole::ADMIN;
    }
}

