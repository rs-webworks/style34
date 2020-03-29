<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Role\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\Identifier;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Role
 *
 * @ORM\Table(name="profile_roles")
 * @ORM\Entity(repositoryClass="EryseClient\Client\Profile\Role\Repository\RoleRepository")
 * @UniqueEntity("name")
 */
class RoleEntity implements ClientEntity
{
    public const DELETED = 'ROLE_DELETED';
    public const BANNED = 'ROLE_BANNED';
    public const INACTIVE = 'ROLE_INACTIVE';
    public const MEMBER = 'ROLE_MEMBER';
    public const MODERATOR = 'ROLE_MODERATOR';
    public const ADMIN = 'ROLE_ADMIN';

    use Identifier;

    /**
     * @var string $name
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="profile.role.name-required")
     */
    protected string $name;

    /**
     * @var string $color
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="profile.role.color-required")
     */
    protected string $color;

    /**
     * @var ProfileEntity[]|Collection
     * @ORM\OneToMany(targetEntity="EryseClient\Client\Profile\Entity\ProfileEntity", mappedBy="role")
     */
    protected Collection $profiles;

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getColor() : string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color) : void
    {
        $this->color = $color;
    }

    /**
     * @return ProfileEntity[]|Collection
     */
    public function getProfiles() : array
    {
        return $this->profiles;
    }

    /**
     * @param ProfileEntity[]|Collection $profiles
     */
    public function setProfiles(array $profiles) : void
    {
        $this->profiles = $profiles;
    }

}
