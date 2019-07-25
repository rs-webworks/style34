<?php declare(strict_types=1);

namespace EryseClient\Entity\Client\Token;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\CreatedAt;
use EryseClient\Entity\Common\ExpiresAt;
use EryseClient\Entity\Common\Identifier;
use EryseClient\Entity\Server\User\User;


/**
 * Class Token
 * @package EryseClient\Entity\Token
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Client\Token\TokenRepository")
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
     * @ORM\ManyToOne(targetEntity="EryseClient\Entity\Token\TokenType", inversedBy="tokens")
     */
    protected $type;

    /**
     * @var User $user ;
     * @ORM\ManyToOne(targetEntity="EryseClient\Entity\User\User", inversedBy="tokens")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

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