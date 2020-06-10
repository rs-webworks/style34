<?php declare(strict_types=1);

namespace EryseClient\Server\User\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Common\Entity\CreatedAt;
use EryseClient\Common\Entity\DeletedAt;
use EryseClient\Common\Entity\ServerEntity;
use EryseClient\Server\User\Device\Entity\DeviceEntity;
use EryseClient\Server\User\Role\Entity\RoleEntity;
use Ramsey\Uuid\Uuid;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * @ORM\Entity(repositoryClass="EryseClient\Server\User\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("username", message="user.username-taken")
 * @UniqueEntity("email", message="user.email-taken")
 */
class UserEntity implements UserInterface, TrustedDeviceInterface, ServerEntity
{
    use CreatedAt;
    use DeletedAt;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    protected string $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    protected string $username;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    protected string $email;

    /**
     * @ORM\Column(type="string")
     */
    protected string $password;

    /**
     * TODO: Remove? Is in Validator
     *
     * @var string
     */
    protected string $plainPassword;

    /**
     * @var DateTime|null $activatedAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ? DateTime $activatedAt;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected string $role;

    /**
     * @var RoleEntity
     */
    protected RoleEntity $roleEntity;

    /**
     * @var string $lastIp
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $lastIp;

    /**
     * @var string $registeredAs
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $registeredAs;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    protected int $trustedTokenVersion;

    /**
     * @var DeviceEntity[]|Collection
     * @ORM\OneToMany(
     *     targetEntity="EryseClient\Server\User\Device\Entity\DeviceEntity",
     *     mappedBy="user",
     *     cascade={"persist"}
     * )
     */
    protected ? Collection $devices;

    /**
     * @var ProfileEntity|null
     */
    protected ? ProfileEntity $profile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ? string $state;

    /**
     * @ORM\Column(nullable=true, type="string")
     */
    protected ? string $city;

    /**
     * @var DateTime $birthdate
     * @ORM\Column(nullable=true, type="datetime")
     */
    protected ? DateTime $birthdate;

    /**
     * @var string|null
     * @ORM\Column(nullable=true, type="string")
     */
    protected ? string $occupation;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->setId(Uuid::uuid4()->toString());
        $this->setRole(RoleEntity::INACTIVE);
        $this->trustedTokenVersion = 0;
    }

    /**
     *
     */
    public function eraseCredentials() : void
    {
        // TODO: Implement eraseCredentials() method.
    }

    // ID
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string
     */
    final public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }


    // Username
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string|null
     */
    public function getUsername() : ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }


    // Email
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string|null
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email) : void
    {
        $this->email = $email;
    }


    // Password
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string|null
     */
    public function getPassword() : ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword() : ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword) : void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string|null
     */
    public function getSalt() : ?string
    {
        // TODO: Implement getSalt() method.
        return null;
    }


    // Activated At
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return DateTime|null
     */
    public function getActivatedAt() : ?DateTime
    {
        return $this->activatedAt;
    }

    /**
     * @param DateTime|null $activatedAt
     */
    public function setActivatedAt(?DateTime $activatedAt) : void
    {
        $this->activatedAt = $activatedAt;
    }

    // Role
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string
     */
    public function getRole() : string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role) : void
    {
        $this->role = $role;
    }

    /**
     * This is used internally in Symfony security
     *
     * @return array
     */
    public function getRoles() : array
    {
        return [$this->getRole()];
    }

    /**
     * @return RoleEntity
     */
    public function getRoleEntity() : RoleEntity
    {
        return $this->roleEntity;
    }

    // Last Ip
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string
     */
    public function getLastIp() : string
    {
        return $this->lastIp;
    }

    /**
     * @param string $lastIp
     */
    public function setLastIp(string $lastIp) : void
    {
        $this->lastIp = $lastIp;
    }


    // Registered as
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string
     */
    public function getRegisteredAs() : string
    {
        return $this->registeredAs;
    }

    /**
     * @param string $registeredAs
     */
    public function setRegisteredAs(string $registeredAs) : void
    {
        $this->registeredAs = $registeredAs;
    }

    // Devices
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return array
     */
    public function getDevices() : array
    {
        return $this->devices;
    }

    /**
     * @param array $devices
     */
    public function setDevices(array $devices) : void
    {
        $this->devices = $devices;
    }

    /**
     * @param DeviceEntity $device
     */
    public function addDevice(DeviceEntity $device) : void
    {
        if (!in_array($device, $this->devices, true)) {
            $this->devices[] = $device;
        }
    }

    /**
     * @param DeviceEntity $device
     */
    public function removeDevice(DeviceEntity $device) : void
    {
        unset($this->devices[array_search($device, $this->devices, true)]);
    }

    /**
     * @return int
     */
    public function getTrustedTokenVersion() : int
    {
        return $this->trustedTokenVersion;
    }

    /**
     * @param int $version
     */
    public function setTrustedTokenVersion(int $version) : void
    {
        $this->trustedTokenVersion = $version;
    }

    // Profile
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return ProfileEntity|null
     */
    public function getProfile() : ?ProfileEntity
    {
        return $this->profile;
    }

    /**
     * @param ProfileEntity $profile
     */
    public function setProfile(ProfileEntity $profile) : void
    {
        $this->profile = $profile;
    }

    // Birthdate
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return DateTime
     */
    public function getBirthdate() : DateTime
    {
        return $this->birthdate;
    }

    /**
     * @param DateTime $birthdate
     */
    public function setBirthdate(DateTime $birthdate) : void
    {
        $this->birthdate = $birthdate;
    }

    // State
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string|null
     */
    public function getState() : ?string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state) : void
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
    public function setCity($city) : void
    {
        $this->city = $city;
    }

    // Occupation
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string
     */
    public function getOccupation() : ?string
    {
        return $this->occupation;
    }

    /**
     * @param string $occupation
     */
    public function setOccupation(?string $occupation) : void
    {
        $this->occupation = $occupation;
    }
}
