<?php declare(strict_types=1);

namespace EryseClient\Server\UserRole\Entity;

/**
 * Class Role
 * @package EryseClient\Entity\Server\User
 */
class UserRole
{
    public const USER = 'ROLE_USER';
    public const DELETED = 'ROLE_DELETED';
    public const BANNED = 'ROLE_BANNED';
    public const INACTIVE = 'ROLE_INACTIVE';
    public const VERIFIED = 'ROLE_VERIFIED';
    public const ADMIN = 'ROLE_ADMIN';
}
