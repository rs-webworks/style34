<?php

namespace Style34\Entity\Profile;

use Doctrine\ORM\Mapping as ORM;
use Style34\Entity\Identifier;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Role
 * @package Style34\Entity\Profile
 * @ORM\Entity(repositoryClass="Style34\Repository\Profile\RoleRepository")
 * @UniqueEntity("name")
 */
class Role {

    const ADMIN = 'ROLE_ADMIN';
    const MODERATOR = 'ROLE_MODERATOR';
    const MEMBER = 'ROLE_MEMBER';
    const INACTIVE = 'ROLE_INACTIVE';
    const BANNED = 'ROLE_BANNED';

    use Identifier;

    /**
     * @var string $name
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="profile.role.name-required")
     */
    protected $name;

    /**
     * @var Profile[]
     * @ORM\OneToMany(targetEntity="Style34\Entity\Profile\Profile", mappedBy="roles")
     */
    protected $profiles;

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
     * @return Profile[]
     */
    public function getProfiles(): array {
        return $this->profiles;
    }

    /**
     * @param Profile[] $profiles
     */
    public function setProfiles(array $profiles): void {
        $this->profiles = $profiles;
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