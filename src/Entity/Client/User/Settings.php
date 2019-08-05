<?php declare(strict_types=1);

namespace EryseClient\Entity\Client\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\Identifier;
use EryseClient\Entity\Server\User\User;

/**
 * Class Settings
 * @package EryseClient\Entity\Client\User
 * @ORM\Table(name="user_settings")
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Client\User\SettingsRepository")
 */
class Settings
{

    use Identifier;

    /**
     * @var User
     * @ORM\Column(type="integer", nullable=false)
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


    public function __construct(User $user = null)
    {
        $this->setUser($user);
        $this->twoStepAuthEnabled = false;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function isTwoStepAuthEnabled(): bool
    {
        return $this->twoStepAuthEnabled;
    }

    public function setTwoStepAuthEnabled(bool $twoStepAuthEnabled): void
    {
        $this->twoStepAuthEnabled = $twoStepAuthEnabled;
    }

    public function getGAuthSecret(): string
    {
        return $this->gAuthSecret;
    }

    public function setGAuthSecret(?string $gAuthSecret): void
    {
        $this->gAuthSecret = $gAuthSecret;
    }


}