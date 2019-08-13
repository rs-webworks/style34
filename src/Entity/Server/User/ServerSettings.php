<?php declare(strict_types=1);

namespace EryseClient\Entity\Server\User;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\Identifier;
use EryseClient\Entity\Server\User\User;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

/**
 * Class ServerSettings
 * @package EryseClient\Entity\Server\User
 * @ORM\Table(name="user_settings")
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Server\User\ServerSettingsRepository")
 */
class ServerSettings
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


    public function __construct(User $user = null)
    {
        $this->setUserId($user->getId());
        $this->twoStepAuthEnabled = false;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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