<?php declare(strict_types=1);

namespace EryseClient\Model\Client\Profile\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use EryseClient\Model\Client\ProfileRole\Entity\ProfileRole;
use EryseClient\Model\Common\Entity\Identifier;
use EryseClient\Model\Server\User\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Profile
 * @package EryseClient\Entity\Client\Profile
 * @ORM\Table(name="profiles")
 * @ORM\Entity(repositoryClass="EryseClient\Model\Client\Profile\Repository\ProfileRepository")
 */
class Profile implements UserInterface
{

    use Identifier;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $userId;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var ProfileRole
     * @ORM\ManyToOne(targetEntity="EryseClient\Model\Client\ProfileRole\Entity\ProfileRole", inversedBy="profiles")
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
     * This is used only internally by symfony/security. Use $this->getRole() in order to get user role directly.
     * @inheritDoc
     */
    public function getRoles()
    {
        return [$this->role->getName()];
    }

    /**
     * @inheritDoc
     * @return string|void
     */
    public function getPassword()
    {
        $this->getUser()
            ->getPassword();
    }

    /**
     * @inheritDoc
     * @return string|void|null
     */
    public function getSalt()
    {
        $this->getUser()
            ->getSalt();
    }

    /**
     * @inheritDoc
     * @return string|void
     */
    public function getUsername()
    {
        $this->getUser()
            ->getUsername();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        $this->user->eraseCredentials();
    }



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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
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

    // City
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return ProfileRole
     */
    public function getRole(): ProfileRole
    {
        return $this->role;
    }

    /**
     * @param ProfileRole $role
     */
    public function setRole(ProfileRole $role): void
    {
        $this->role = $role;
    }
}
