<?php declare(strict_types=1);

namespace EryseClient\Model\Client\ProfileSettings\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Model\Common\Entity\Identifier;
use EryseClient\Model\Server\User\Entity\User;

/**
 * Class Settings
 * @package EryseClient\Entity\Client\User
 * @ORM\Table(name="user_settings")
 * @ORM\Entity(repositoryClass="EryseClient\Model\Client\ProfileSettings\Repository\ProfileSettingsRepository")
 */
class ProfileSettings
{
    use Identifier;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $userId;

    /**
     * ClientSettings constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->setUserId($user->getId());
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
}
