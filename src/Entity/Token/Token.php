<?php declare(strict_types=1);

namespace Style34\Entity\Token;

use Doctrine\ORM\Mapping as ORM;
use Style34\Entity\CreatedAt;
use Style34\Entity\ExpiresAt;
use Style34\Entity\Identifier;
use Style34\Entity\MasterData;
use Style34\Entity\Profile\Profile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Token
 * @package Style34\Entity\Token
 * @ORM\Entity()
 */
class Token
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
     * @var TokenType $type
     * @ORM\ManyToOne(targetEntity="Style34\Entity\Token\TokenType", inversedBy="tokens")
     */
    protected $type;

    /**
     * @var Profile $profile;
     * @ORM\ManyToOne(targetEntity="Style34\Entity\Profile\Profile", inversedBy="tokens")
     */
    protected $profile;

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


}