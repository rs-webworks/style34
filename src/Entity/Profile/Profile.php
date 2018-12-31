<?php declare(strict_types=1);

namespace eRyseClient\Entity\Profile;

use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use eRyseClient\Entity\CreatedAt;
use eRyseClient\Entity\DeletedAt;
use eRyseClient\Entity\Token\Token;
use eRyseClient\Service\CryptService;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Profile
 * @package eRyseClient\Entity\Profile
 * @ORM\Entity(repositoryClass="eRyseClient\Repository\Profile\ProfileRepository")
 * @UniqueEntity("username", message="profile.username-taken")
 * @UniqueEntity("email", message="profile.email-taken")
 */
class Profile implements UserInterface, TwoFactorInterface, TrustedDeviceInterface
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
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull(message="profile.state-required")
     */
    protected $state;

    /**
     * @ORM\Column(nullable=true, type="string")
     */
    protected $city;

    /**
     * @var \DateTime $birthdate
     * @ORM\Column(nullable=true, type="datetime")
     */
    protected $birthdate;

    /**
     * @var Token[] $tokens
     * @ORM\OneToMany(targetEntity="eRyseClient\Entity\Token\Token", mappedBy="profile", cascade={"persist"},
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
     * @ORM\OneToOne(targetEntity="eRyseClient\Entity\Profile\Settings", mappedBy="profile",  cascade={"persist"})
     */
    protected $settings;

    /**
     * @var Device[]
     * @ORM\OneToMany(targetEntity="Device", mappedBy="profile",  cascade={"persist"})
     */
    protected $devices;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $trustedTokenVersion;


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


    // Birthdate
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return \DateTime
     */
    public function getBirthdate(): \DateTime
    {
        return $this->birthdate;
    }

    /**
     * @param \DateTime $birthdate
     */
    public function setBirthdate(\DateTime $birthdate): void
    {
        $this->birthdate = $birthdate;
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


    // State
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
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
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
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
        return CryptService::getDecrypted($this->getSettings()->getGAuthSecret());
    }

    /**
     * @param string|null $googleAuthenticatorSecret
     */
    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void
    {
        $this->getSettings()->setGAuthSecret(CryptService::getEncrypted($googleAuthenticatorSecret));
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