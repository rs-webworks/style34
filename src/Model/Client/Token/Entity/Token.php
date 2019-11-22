<?php declare(strict_types=1);

namespace EryseClient\Model\Client\Token\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Model\Common\Entity\CreatedAt;
use EryseClient\Model\Common\Entity\ExpiresAt;
use EryseClient\Model\Common\Entity\Identifier;

/**
 * Class Token
 * @package EryseClient\Entity\Client\Token
 * @ORM\Table(name="tokens")
 * @ORM\Entity(repositoryClass="EryseClient\Model\Client\Token\Repository\TokenRepository")
 */
class Token
{
    const EXPIRY_MINUTE = 60;
    const EXPIRY_HOUR = self::EXPIRY_MINUTE * 60;
    const EXPIRY_DAY = self::EXPIRY_HOUR * 24;
    const EXPIRY_WEEK = self::EXPIRY_DAY * 7;
    const EXPIRY_MONTH = self::EXPIRY_DAY * 30;

    use Identifier;
    use CreatedAt;
    use ExpiresAt;

    /**
     * @var string $hash
     * @ORM\Column(type="string")
     */
    protected $hash;

    /**
     * @var TokenType $type
     * @ORM\ManyToOne(targetEntity="EryseClient\Model\Client\Token\Entity\TokenType", inversedBy="tokens")
     */
    protected $type;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $userId;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $invalid = false;

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return TokenType
     */
    public function getType(): TokenType
    {
        return $this->type;
    }

    /**
     * @param TokenType $type
     */
    public function setType(TokenType $type): void
    {
        $this->type = $type;
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
    public function isInvalid(): bool
    {
        return $this->invalid;
    }

    /**
     * @param bool $invalid
     */
    public function setInvalid(bool $invalid): void
    {
        $this->invalid = $invalid;
    }
}
