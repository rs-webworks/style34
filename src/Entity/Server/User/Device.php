<?php declare(strict_types=1);

namespace EryseClient\Entity\Server\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\Identifier;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Device
 * @package EryseClient\Entity\User
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Server\User\DeviceRepository")
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    public function setCookieName(string $cookieName): void
    {
        $this->cookieName = $cookieName;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }


}