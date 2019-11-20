<?php declare(strict_types=1);

namespace EryseClient\Server\UserDevice\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\Identifier;
use EryseClient\Server\User\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Device
 * @package EryseClient\Entity\Server\User
 * @ORM\Table(name="user_devices")
 * @ORM\Entity(repositoryClass="UserDeviceRepository")
 * @UniqueEntity("name")
 */
class UserDevice
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
     * @ORM\ManyToOne(targetEntity="EryseClient\Entity\Server\User\User", inversedBy="devices")
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
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
