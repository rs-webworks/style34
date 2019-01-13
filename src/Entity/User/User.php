<?php declare(strict_types=1);

namespace EryseClient\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\CreatedAt;
use EryseClient\Entity\DeletedAt;
use EryseClient\Entity\Profile\Profile;
use EryseClient\Entity\Token\Token;
use EryseClient\Service\CryptService;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package EryseClient\Entity\User
 * @ORM\Entity(repositoryClass="EryseClient\Repository\User\UserRepository")
 * @ORM\Table(name="global_users")
 * @UniqueEntity("username", message="user.username-taken")
 * @UniqueEntity("email", message="user.email-taken")
 */
class User implements UserInterface, TwoFactorInterface, TrustedDeviceInterface
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
     * @var \DateTime $activatedAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $activatedAt;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    protected $roles = [];


    /**
     * @var Token[] $tokens
     * @ORM\OneToMany(targetEntity="EryseClient\Entity\Token\Token", mappedBy="user", cascade={"persist"},
     *                                                           orphanRemoval=true)
     */
    protected $tokens;

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
     * @var Settings
     * @ORM\OneToOne(targetEntity="EryseClient\Entity\User\Settings", mappedBy="user", cascade={"persist"})
     */
    protected $settings;

    /**
     * @var Device[]
     * @ORM\OneToMany(targetEntity="EryseClient\Entity\User\Device", mappedBy="user",  cascade={"persist"})
     */
    protected $devices;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $trustedTokenVersion;

    /**
     * @var Profile
     * @ORM\OneToOne(targetEntity="EryseClient\Entity\Profile\Profile", inversedBy="user", cascade={"persist"})
     */
    protected $profile;

    // Methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Profile constructor.
     */
    public function __construct()
    {
        $this->addRole(Role::USER);
        $this->trustedTokenVersion = 0;
    }

    /**
     *
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    // ID
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return int
     */
    final public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;

    }


    // Username
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }


    // Email
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    // Password
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return null|string|void
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }


    // Activated At
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return \DateTime
     */
    public function getActivatedAt(): \DateTime
    {
        return $this->activatedAt;
    }

    /**
     * @param \DateTime $activatedAt
     */
    public function setActivatedAt(\DateTime $activatedAt): void
    {
        $this->activatedAt = $activatedAt;
    }

    // Roles
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string $role
     */
    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            array_push($this->roles, $role);
        }
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param string $role
     */
    public function removeRole(string $role): void
    {
        unset($this->roles[array_search($role, $this->roles)]);
    }

    // Tokens
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Token[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param Token[] $tokens
     */
    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }


    // Last Ip
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getLastIp(): string
    {
        return $this->lastIp;
    }

    /**
     * @param string $lastIp
     */
    public function setLastIp(string $lastIp): void
    {
        $this->lastIp = $lastIp;
    }


    // Registered as
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getRegisteredAs(): string
    {
        return $this->registeredAs;
    }

    /**
     * @param string $registeredAs
     */
    public function setRegisteredAs(string $registeredAs): void
    {
        $this->registeredAs = $registeredAs;
    }


    // Settings
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Settings
     */
    public function getSettings(): Settings
    {
        return $this->settings;
    }

    /**
     * @param Settings $settings
     */
    public function setSettings(Settings $settings): void
    {
        $this->settings = $settings;
    }

    // Two step authentificator
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->getSettings()->isTwoStepAuthEnabled();
    }

    /**
     * @return string
     */
    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->getEmail();
    }

    /**
     * @return string
     */
    public function getGoogleAuthenticatorSecret(): string
    {
        return $this->getSettings()->getGAuthSecret();
    }

    /**
     * @param string|null $googleAuthenticatorSecret
     */
    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void
    {
        $this->getSettings()->setGAuthSecret($googleAuthenticatorSecret);
    }


    // Devices
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Device[]
     */
    public function getDevices(): array
    {
        return $this->devices;
    }

    /**
     * @param Device[] $devices
     */
    public function setDevices(array $devices): void
    {
        $this->devices = $devices;
    }

    /**
     * @param Device $device
     */
    public function addDevice(Device $device): void
    {
        if (!in_array($device, $this->devices)) {
            array_push($this->devices, $device);
        }
    }

    /**
     * @param Device $device
     */
    public function removeDevice(Device $device): void
    {
        unset($this->devices[array_search($device, $this->devices)]);
    }

    /**
     * @return int
     */
    public function getTrustedTokenVersion(): int
    {
        return $this->trustedTokenVersion;
    }

    /**
     * @param int $version
     */
    public function setTrustedTokenVersion(int $version): void
    {
        $this->trustedTokenVersion = $version;
    }
}