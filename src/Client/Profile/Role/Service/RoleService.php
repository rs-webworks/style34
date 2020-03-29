<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Role\Service;

use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Client\Profile\Role\Entity\RoleEntity;
use EryseClient\Common\Service\AbstractService;

/**
 * Class ProfileRoleService
 *
 *
 */
class RoleService extends AbstractService
{
    private const BLOCKED_ROLES = [
        RoleEntity::BANNED,
        RoleEntity::DELETED
    ];

    private const ALLOWED_ROLES = [
        RoleEntity::MEMBER,
        RoleEntity::ADMIN,
        RoleEntity::MODERATOR
    ];

    /**
     * @param string $role
     * @return bool
     */
    public function isRoleBlocked(string $role): bool
    {
        return in_array($role, self::BLOCKED_ROLES, true);
    }

    /**
     * @return array
     */
    public function getAllowedRolesList(): array
    {
        return self::ALLOWED_ROLES;
    }

    /**
     * @param ProfileEntity $profile
     *
     * @return bool
     */
    public function isRoleAdmin(ProfileEntity $profile): bool
    {
        return $profile->getRole()->getName() === RoleEntity::ADMIN;
    }

    /**
     * @param ProfileEntity $profile
     *
     * @return bool
     */
    public function isRoleModerator(ProfileEntity $profile): bool
    {
        return $profile->getRole()->getName() === RoleEntity::MODERATOR;
    }

}
