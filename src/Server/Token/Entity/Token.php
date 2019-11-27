<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\CreatedAt;
use EryseClient\Common\Entity\ExpiresAt;
use EryseClient\Common\Entity\Identifier;
use EryseClient\Common\Token\TokenInterface;
use EryseClient\Common\Token\TokenTypeInterfance;
use EryseClient\Server\User\Entity\User;

/**
 * Class Token
 * @package EryseClient\Entity\Client\Token
 * @ORM\Table(name="tokens")
 * @ORM\Entity(repositoryClass="EryseClient\Server\Token\Repository\TokenRepository")
 */
class Token implements ClientEntity, TokenInterface
{
    use Identifier;
    use CreatedAt;
    use ExpiresAt;

    /**
     * @var string $hash
     * @ORM\Column(type="string")
     */
    protected $hash;

    /**
     * @var TokenTypeInterfance $type
     * @ORM\ManyToOne(targetEntity="EryseClient\Server\Token\Entity\TokenType", inversedBy="tokens")
     */
    protected $type;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="EryseClient\Server\User\Entity\User")
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
     * @return TokenTypeInterfance
     */
    public function getType(): TokenTypeInterfance
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

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return !$this->isInvalid();
    }


}
