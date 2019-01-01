<?php declare(strict_types=1);

namespace EryseClient\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Identifier;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Device
 * @package EryseClient\Entity\User
 * @ORM\Entity(repositoryClass="EryseClient\Repository\User\DeviceRepository")
 * @UniqueEntity("name")
 */
class Device
{

    use Identifier;

    /**
     * @var string $name
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="profile.role.name-required")
     */
    protected $name;

    /**
     * @var string $cookieName
     * @ORM\Column(type="string", unique=true)
     */
    protected $cookieName;

    /**
     * @var User $user
     * @ORM\ManyToOne(targetEntity="EryseClient\Entity\User\User", inversedBy="devices")
     */
    protected $user;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    /**
     * @param string $cookieName
     */
    public function setCookieName(string $cookieName): void
    {
        $this->cookieName = $cookieName;
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
     * @return User
     */
    public function setUser(User $user): User
    {
        $this->user = $user;
    }


}