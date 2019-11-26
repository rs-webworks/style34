<?php declare(strict_types=1);

namespace EryseClient\Client\ProfileRole\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\Identifier;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Role
 * @package EryseClient\Client\Profile\Entity
 * @ORM\Table(name="profile_roles")
 * @ORM\Entity(repositoryClass="EryseClient\Client\ProfileRole\Repository\ProfileRoleRepository")
 * @UniqueEntity("name")
 */
class ProfileRole implements ClientEntity
{
    public const DELETED = 'ROLE_DELETED';
    public const BANNED = 'ROLE_BANNED';
    public const INACTIVE = 'ROLE_INACTIVE';
    public const VERIFIED = 'ROLE_VERIFIED';
    public const MEMBER = 'ROLE_MEMBER';
    public const MODERATOR = 'ROLE_MODERATOR';
    public const ADMIN = 'ROLE_ADMIN';

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
     * @var Profile[]
     * @ORM\OneToMany(targetEntity="EryseClient\Client\Profile\Entity\Profile", mappedBy="role")
     */
    protected $profiles;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return Profile[]
     */
    public function getProfiles(): array
    {
        return $this->profiles;
    }

    /**
     * @param Profile[] $profiles
     */
    public function setProfiles(array $profiles): void
    {
        $this->profiles = $profiles;
    }


}
