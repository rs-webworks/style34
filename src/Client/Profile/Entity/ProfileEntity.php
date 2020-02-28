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
     * @var int
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    protected $userId;

    /**
     * @var UserEntity
     */
    protected $user;

    /**
     * @var RoleEntity
     * @ORM\ManyToOne(targetEntity="EryseClient\Client\Profile\Role\Entity\RoleEntity", inversedBy="profiles",
     *     fetch="EAGER")
     */
    protected $role;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull(message="profile.state-required")
     */
    protected $state;

    /**
     * @ORM\Column(nullable=true, type="string")
     */
    protected $city;

    /**
     * @var DateTime $birthdate
     * @ORM\Column(nullable=true, type="datetime")
     */
    protected $birthdate;

    /**
     * @var string|null
     * @ORM\Column(nullable=true, type="string")
     */
    protected $occupation;


    // User
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return UserEntity|null
     */
    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     */
    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }


    // Birthdate
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return DateTime
     */
    public function getBirthdate(): DateTime
    {
        return $this->birthdate;
    }

    /**
     * @param DateTime $birthdate
     */
    public function setBirthdate(DateTime $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    // State
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }


    // City
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    // ProfileRole
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return RoleEntity
     */
    public function getRole(): RoleEntity
    {
        return $this->role;
    }

    /**
     * @param RoleEntity $role
     */
    public function setRole(RoleEntity $role): void
    {
        $this->role = $role;
    }

    // Occupation
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string
     */
    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    /**
     * @param string $occupation
     *
     * @return ProfileEntity
     */
    public function setOccupation(?string $occupation): ProfileEntity
    {
        $this->occupation = $occupation;

        return $this;
    }

}
