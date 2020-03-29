<?php declare(strict_types=1);

namespace EryseClient\Server\User\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Common\Entity\CreatedAt;
use EryseClient\Common\Entity\DeletedAt;
use EryseClient\Common\Entity\ServerEntity;
use EryseClient\Server\User\Device\Entity\DeviceEntity;
use EryseClient\Server\User\Role\Entity\RoleEntity;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="user.username-required")
     * @Assert\Length(min=3, max=20,
     *      minMessage="user.username-min-length",
     *      maxMessage="user.username-max-length",
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9]+(?:[ _-][A-Za-z0-9]+)*$/",
     *     message="user.username-invalid"
     * )
     */
    protected string $username;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\Email(message="user.email-invalid")
     * @Assert\NotBlank(message="user.email-required")
     */
    protected string $email;

    /**
     * @ORM\Column(type="string")
     */
    protected string $password;

    /**
     * @Assert\NotBlank(message="user.password-required")
     * @Assert\Length(min=6, max=4096,
     *     minMessage="user.password-min-length",
     *     maxMessage="user.password-max.length"
     * )
     */
    protected string $plainPassword;

    /**
     * @var DateTime $activatedAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected DateTime $activatedAt;

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
     * @Assert\Ip(message="user.ip-expected")
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
     * @var DeviceEntity[]
     * @ORM\OneToMany(
     *     targetEntity="EryseClient\Server\User\Device\Entity\DeviceEntity",
     *     mappedBy="user",
     *     cascade={"persist"}
     * )
     */
    protected array $devices;

    /**
     * @var ProfileEntity|null
     */
    protected ? ProfileEntity $profile;

    /**
     * User constructor.
     */
    public function __construct()
    {
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
     * @return int
     */
    final public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) : void
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
     * @param DateTime $activatedAt
     */
    public function setActivatedAt(DateTime $activatedAt) : void
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
}
