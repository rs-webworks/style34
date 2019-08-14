<?php declare(strict_types=1);

namespace EryseClient\Entity\Client\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\Identifier;
use EryseClient\Entity\Server\User\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Role
 * @package EryseClient\Entity\Client\User
 * @ORM\Table(name="user_roles")
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Client\User\RoleRepository")
 * @ORM\EntityListeners({"EryseClient\EntityListener\Client\User\RoleListener"})
 * @UniqueEntity("name")
 */
class Role
{

    public const ADMIN = 'ROLE_ADMIN';
    public const MODERATOR = 'ROLE_MODERATOR';
    public const MEMBER = 'ROLE_MEMBER';
    public const INACTIVE = 'ROLE_INACTIVE';
    public const USER = 'ROLE_USER';
    public const VERIFIED = 'ROLE_VERIFIED';
    public const BANNED = 'ROLE_BANNED';
    public const SERVER_BANNED = 'ROLE_SERVER_BANNED';

    use Identifier;

    /**
     * @var string $name
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="profile.role.name-required")
     */
    protected $name;

    /**
     * @var string $color
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="profile.role.color-required")
     */
    protected $color;

    /**
     * @var User[]
     */
    protected $users;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setUsers(array $users): void
    {
        $this->users = $users;
    }



}