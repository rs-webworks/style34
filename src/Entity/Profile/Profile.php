<?php declare(strict_types=1);

namespace Style34\Entity\Profile;

use Doctrine\ORM\Mapping as ORM;
use Style34\Entity\Address\State;
use Style34\Entity\CreatedAt;
use Style34\Entity\DeletedAt;
use Style34\Entity\Identifier;
use Style34\Entity\Token\Token;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Profile
 * @package Style34\Entity\Profile
 * @ORM\Entity(repositoryClass="Style34\Repository\Profile\ProfileRepository")
 * @UniqueEntity("username", message="profile.username-taken")
 * @UniqueEntity("email", message="profile.email-taken")
 */
class Profile implements UserInterface
{


    use Identifier;
    use CreatedAt;
    use DeletedAt;

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
     * @var Role $role
     * @ORM\ManyToOne(targetEntity="Style34\Entity\Profile\Role", inversedBy="profiles")
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
     * @var \DateTime $birthdate
     * @ORM\Column(nullable=true, type="datetime")
     */
    protected $birthdate;

    /**
     * @var Token[] $tokens
     * @ORM\OneToMany(targetEntity="Style34\Entity\Token\Token", mappedBy="profile")
     */
    protected $tokens;


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


    /**
     * For Symfony security
     * @return array
     */
    public function getRoles(): ?array
    {
        $roles[] = $this->role->getName();

        return array_unique($roles);
    }

    /**
     * @return Role
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

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


    /**
     *
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return null|string|void
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

}