<?php declare(strict_types=1);

namespace EryseClient\Client\ProfileRole\Service;

use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Client\ProfileRole\Entity\ProfileRole;
use EryseClient\Common\Service\AbstractService;

/**
 * Class ProfileRoleService
 *
 * @package EryseClient\Client\ProfileRole\Service
 */
class ProfileRoleService extends AbstractService
{
    private const BLOCKED_ROLES = [
        ProfileRole::BANNED,
        ProfileRole::DELETED
    ];

    private const ALLOWED_ROLES = [
        ProfileRole::MEMBER,
        ProfileRole::ADMIN,
        ProfileRole::MODERATOR
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
     * @return array
     */
    public function getAllowedRolesList(): array
    {
        return self::ALLOWED_ROLES;
    }

    /**
     * @param Profile $profile
     * @return bool
     */
    public function isRoleAdmin(Profile $profile): bool
    {
        return $profile->getRole()->getName() == ProfileRole::ADMIN;
    }

    /**
     * @param Profile $profile
     * @return bool
     */
    public function isRoleModerator(Profile $profile): bool
    {
        return $profile->getRole()->getName() == ProfileRole::MODERATOR;
    }

}
