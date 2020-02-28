<?php declare(strict_types=1);

namespace EryseClient\Server\User\Device\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\Identifier;
use EryseClient\Common\Entity\ServerEntity;
use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Device
 *
 * @ORM\Table(name="user_devices")
 * @ORM\Entity(repositoryClass="EryseClient\Server\User\Device\Repository\DeviceRepository")
 * @UniqueEntity("name")
 */
class DeviceEntity implements ServerEntity
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
     * @var UserEntity $user
     * @ORM\ManyToOne(targetEntity="EryseClient\Server\User\Entity\UserEntity", inversedBy="devices")
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
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     */
    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }
}
