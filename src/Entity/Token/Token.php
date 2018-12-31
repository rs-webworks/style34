<?php declare(strict_types=1);

namespace eRyseClient\Entity\Token;

use Doctrine\ORM\Mapping as ORM;
use eRyseClient\Entity\CreatedAt;
use eRyseClient\Entity\ExpiresAt;
use eRyseClient\Entity\Identifier;
use eRyseClient\Entity\Profile\Profile;

/**
 * Class Token
 * @package eRyseClient\Entity\Token
 * @ORM\Entity(repositoryClass="eRyseClient\Repository\Token\TokenRepository")
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
     * @ORM\ManyToOne(targetEntity="eRyseClient\Entity\Token\TokenType", inversedBy="tokens")
     */
    protected $type;

    /**
     * @var Profile $profile ;
     * @ORM\ManyToOne(targetEntity="eRyseClient\Entity\Profile\Profile", inversedBy="tokens")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $profile;

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
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile): void
    {
        $this->profile = $profile;
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