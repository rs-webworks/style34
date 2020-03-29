<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use EryseClient\Client\Profile\Role\Entity\RoleEntity;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\Identifier;
use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Profile
 *
 * @ORM\Table(name="profiles")
 * @ORM\Entity(repositoryClass="EryseClient\Client\Profile\Repository\ProfileRepository")
 */
class ProfileEntity implements ClientEntity
{

    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected string $userId;

    /**
     * @var UserEntity
     */
    protected UserEntity $user;

    /**
     * @var RoleEntity
     * @ORM\ManyToOne(targetEntity="EryseClient\Client\Profile\Role\Entity\RoleEntity", inversedBy="profiles",
     *     fetch="EAGER")
     */
    protected RoleEntity $role;

    // User
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string
     */
    public function getUserId() : string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId) : void
    {
        $this->userId = $userId;
    }

    /**
     * @return UserEntity|null
     */
    public function getUser() : ?UserEntity
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     */
    public function setUser(UserEntity $user) : void
    {
        $this->user = $user;
    }


    // ProfileRole
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return RoleEntity
     */
    public function getRole() : RoleEntity
    {
        return $this->role;
    }

    /**
     * @param RoleEntity $role
     */
    public function setRole(RoleEntity $role) : void
    {
        $this->role = $role;
    }

}
