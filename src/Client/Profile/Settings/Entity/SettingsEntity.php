<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Settings\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\Identifier;
use EryseClient\Server\User\Entity\UserEntity;

/**
 * Class Settings
 * @ORM\Table(name="user_settings")
 * @ORM\Entity(repositoryClass="EryseClient\Client\Profile\Settings\Repository\SettingsRepository")
 */
class SettingsEntity implements ClientEntity
{
    use Identifier;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $userId;

    /**
     * ClientSettings constructor.
     *
     * @param UserEntity|null $user
     */
    public function __construct(UserEntity $user = null)
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
