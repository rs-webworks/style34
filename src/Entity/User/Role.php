<?php declare(strict_types=1);

namespace EryseClient\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Identifier;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Role
 * @package EryseClient\Entity\User
 * @ORM\Entity(repositoryClass="EryseClient\Repository\User\RoleRepository")
 * @UniqueEntity("name")
 */
class Role {

    const ADMIN = 'ROLE_ADMIN';
    const MODERATOR = 'ROLE_MODERATOR';
    const MEMBER = 'ROLE_MEMBER';
    const INACTIVE = 'ROLE_INACTIVE';
    const USER = 'ROLE_USER';
    const VERIFIED = 'ROLE_VERIFIED';
    const BANNED = 'ROLE_BANNED';

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
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getColor(): string {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void {
        $this->color = $color;
    }


}