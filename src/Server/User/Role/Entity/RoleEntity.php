<?php declare(strict_types=1);

namespace EryseClient\Server\User\Role\Entity;

/**
 * Class Role
 *
 */
class RoleEntity
{
    public const USER = 'ROLE_USER';
    public const DELETED = 'ROLE_DELETED';
    public const BANNED = 'ROLE_BANNED';
    public const INACTIVE = 'ROLE_INACTIVE';
    public const VERIFIED = 'ROLE_VERIFIED';
    public const ADMIN = 'ROLE_ADMIN';
}
