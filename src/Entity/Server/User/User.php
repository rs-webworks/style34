<?php declare(strict_types=1);

namespace EryseClient\Entity\Server\User;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Client\User\Role;
use EryseClient\Entity\Client\User\Settings;
use EryseClient\Entity\Common\CreatedAt;
use EryseClient\Entity\Common\DeletedAt;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package EryseClient\Entity\Server\User
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Server\User\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("username", message="user.username-taken")
 * @UniqueEntity("email", message="user.email-taken")
 */
class User implements UserInterface, TrustedDeviceInterface
{

    use CreatedAt;
    use DeletedAt;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="profile.username-required")
     * @Assert\Length(min=3, max=20,
     *      minMessage="profile.username-min-length",
     *      maxMessage="profile.username-max-length",
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9]+(?:[ _-][A-Za-z0-9]+)*$/",
     *     message="profile.username-invalid"
     * )
     */
    protected $username;


    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\Email(message="profile.email-invalid")
     * @Assert\NotBlank(message="profile.email-required")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @Assert\NotBlank(message="profile.password-required")
     * @Assert\Length(min=6, max=4096,
     *     minMessage="profile.password-min-length",
     *     maxMessage="profile.password-max.length"
     * )
     */
    protected $plainPassword;

    /**
     * @var DateTime $activatedAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $activatedAt;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    protected $roles = [];

    /**
     * @var string $lastIp
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Ip(message="profile.ip-expected")
     */
    protected $lastIp;

    /**
     * @var string $registeredAs
     * @ORM\Column(type="string", nullable=false)
     */
    protected $registeredAs;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $settingsId;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $trustedTokenVersion;

    /**
     * @var Device[]
     * @ORM\OneToMany(targetEntity="EryseClient\Entity\Server\User\Device", mappedBy="user",  cascade={"persist"})
     */
    protected $devices;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $profileId;

    public function __construct()
    {
        $this->addRole(Role::USER);
        $this->trustedTokenVersion = 0;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    // ID
    // -----------------------------------------------------------------------------------------------------------------
    final public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }


    // Username
    // -----------------------------------------------------------------------------------------------------------------
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }


    // Email
    // -----------------------------------------------------------------------------------------------------------------
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    // Password
    // -----------------------------------------------------------------------------------------------------------------
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getSalt(): ?string
    {
        // TODO: Implement getSalt() method.
        return null;
    }


    // Activated At
    // -----------------------------------------------------------------------------------------------------------------
    public function getActivatedAt(): DateTime
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(DateTime $activatedAt): void
    {
        $this->activatedAt = $activatedAt;
    }

    // Roles
    // -----------------------------------------------------------------------------------------------------------------
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            array_push($this->roles, $role);
        }
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function removeRole(string $role): void
    {
        unset($this->roles[array_search($role, $this->roles)]);
    }


    // Last Ip
    // -----------------------------------------------------------------------------------------------------------------
    public function getLastIp(): string
    {
        return $this->lastIp;
    }

    public function setLastIp(string $lastIp): void
    {
        $this->lastIp = $lastIp;
    }


    // Registered as
    // -----------------------------------------------------------------------------------------------------------------
    public function getRegisteredAs(): string
    {
        return $this->registeredAs;
    }

    public function setRegisteredAs(string $registeredAs): void
    {
        $this->registeredAs = $registeredAs;
    }


    // Settings
    // -----------------------------------------------------------------------------------------------------------------
    public function getSettingsId(): ?int
    {
        return $this->settingsId;
    }

    public function setSettingsId(int $settingsId): void
    {
        $this->settingsId = $settingsId;
    }


    // Devices
    // -----------------------------------------------------------------------------------------------------------------
    public function getDevices(): array
    {
        return $this->devices;
    }

    public function setDevices(array $devices): void
    {
        $this->devices = $devices;
    }

    public function addDevice(Device $device): void
    {
        if (!in_array($device, $this->devices)) {
            array_push($this->devices, $device);
        }
    }

    public function removeDevice(Device $device): void
    {
        unset($this->devices[array_search($device, $this->devices)]);
    }

    public function getTrustedTokenVersion(): int
    {
        return $this->trustedTokenVersion;
    }

    public function setTrustedTokenVersion(int $version): void
    {
        $this->trustedTokenVersion = $version;
    }

    // Profile
    // -----------------------------------------------------------------------------------------------------------------
    public function getProfileId(): Int
    {
        return $this->profileId;
    }

    public function setProfileId(int $profileId): void
    {
        $this->profileId = $profileId;
    }


}