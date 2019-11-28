<?php declare(strict_types=1);

namespace EryseClient\Client\ProfileRole\Service;

use EryseClient\Client\ProfileRole\Entity\ProfileRole;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Server\UserRole\Entity\UserRole;

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

    /**
     * @param string $role
     * @return bool
     */
    public function isRoleBlocked(string $role): bool
    {
        return in_array($role, self::BLOCKED_ROLES);
    }

}