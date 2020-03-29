<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\CreatedAt;
use EryseClient\Common\Entity\ExpiresAt;
use EryseClient\Common\Entity\Identifier;
use EryseClient\Common\Token\TokenInterface;
use EryseClient\Common\Token\TokenTypeInterface;
use EryseClient\Server\User\Entity\UserEntity;

/**
 * Class Token
 *
 * @ORM\Table(name="tokens")
 * @ORM\Entity(repositoryClass="EryseClient\Server\Token\Repository\TokenRepository")
 */
class TokenEntity implements ClientEntity, TokenInterface
{
    use Identifier;
    use CreatedAt;
    use ExpiresAt;

    /**
     * @var string $hash
     * @ORM\Column(type="string")
     */
    protected string $hash;

    /**
     * @var TokenTypeInterface $type
     * @ORM\ManyToOne(targetEntity="EryseClient\Server\Token\Type\Entity\TypeEntity", inversedBy="tokens")
     */
    protected TokenTypeInterface $type;

    /**
     * @var UserEntity
     * @ORM\ManyToOne(targetEntity="EryseClient\Server\User\Entity\UserEntity")
     */
    protected UserEntity $user;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected bool $invalid = false;

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
     * @return TokenTypeInterface
     */
    public function getType(): TokenTypeInterface
    {
        return $this->type;
    }

    /**
     * @param TokenTypeInterface $type
     */
    public function setType(TokenTypeInterface $type): void
    {
        $this->type = $type;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     */
    public function setUser(UserEntity $user): void
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
