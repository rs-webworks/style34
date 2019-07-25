<?php declare(strict_types=1);

namespace EryseClient\Entity\Client\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\Identifier;
use EryseClient\Entity\Server\User\User;

/**
 * Class Settings
 * @package EryseClient\Entity\User
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Client\User\SettingsRepository")
 */
class Settings
{

    use Identifier;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="EryseClient\Entity\User\User", inversedBy="settings")
     */
    protected $user;

    /**
     * @var bool $name
     * @ORM\Column(type="boolean")
     */
    protected $twoStepAuthEnabled;

    /**
     * @var string $gAuthSecret
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gAuthSecret;


    /**
     * Settings constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->setUser($user);
        $this->twoStepAuthEnabled = false;
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


    /**
     * @return bool
     */
    public function isTwoStepAuthEnabled(): bool
    {
        return $this->twoStepAuthEnabled;
    }

    /**
     * @param bool $twoStepAuthEnabled
     */
    public function setTwoStepAuthEnabled(bool $twoStepAuthEnabled): void
    {
        $this->twoStepAuthEnabled = $twoStepAuthEnabled;
    }

    /**
     * Always use CryptService to decode
     * @return string
     */
    public function getGAuthSecret(): string
    {
        return $this->gAuthSecret;
    }

    /**
     * Always use CryptService to encode
     * @param string $gAuthSecret
     */
    public function setGAuthSecret(?string $gAuthSecret): void
    {
        $this->gAuthSecret = $gAuthSecret;
    }


}