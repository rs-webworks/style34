<?php declare(strict_types=1);

namespace EryseClient\Model\Server\UserSettings\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Model\Common\Entity\Identifier;
use EryseClient\Model\Server\User\Entity\User;

/**
 * Class ServerSettings
 * @package EryseClient\Entity\Server\User
 * @ORM\Table(name="user_settings")
 * @ORM\Entity(repositoryClass="EryseClient\Model\Server\UserSettings\Repository\UserSettingsRepository")
 */
class UserSettings
{
    use Identifier;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $userId;

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
     * ServerSettings constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->setUserId($user->getId());
        $this->twoStepAuthEnabled = false;
    }

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
     * @return string
     */
    public function getGAuthSecret(): string
    {
        return $this->gAuthSecret;
    }

    /**
     * @param string|null $gAuthSecret
     */
    public function setGAuthSecret(?string $gAuthSecret): void
    {
        $this->gAuthSecret = $gAuthSecret;
    }
}
