<?php declare(strict_types=1);

namespace EryseClient\Server\User\Settings\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\Identifier;
use EryseClient\Common\Entity\ServerEntity;
use EryseClient\Server\User\Entity\UserEntity;

/**
 * Class ServerSettings
 *
 * @ORM\Table(name="user_settings")
 * @ORM\Entity(repositoryClass="EryseClient\Client\Profile\Settings\Repository\SettingsRepository")
 */
class SettingsEntity implements ServerEntity
{
    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $userId;

    /**
     * @var bool $name
     * @ORM\Column(type="boolean")
     */
    protected bool $twoStepAuthEnabled;

    /**
     * @var string|null $gAuthSecret
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $gAuthSecret;

    /**
     * ServerSettings constructor.
     *
     * @param UserEntity|null $user
     */
    public function __construct(UserEntity $user = null)
    {
        $this->setUserId($user->getId());
        $this->twoStepAuthEnabled = false;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
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
     * @return string|null
     */
    public function getGAuthSecret(): ?string
    {
        return $this->gAuthSecret;
    }

    /**
     * @param string|null $gAuthSecret
     */
    public function setGAuthSecret(?string $gAuthSecret = null): void
    {
        $this->gAuthSecret = $gAuthSecret;
    }
}
