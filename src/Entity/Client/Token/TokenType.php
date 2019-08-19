<?php declare(strict_types=1);

namespace EryseClient\Entity\Client\Token;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\MasterData;

/**
 * Class TokenType
 * @package EryseClient\Entity\Client\Token
 * @ORM\Table(name="token_types")
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Client\Token\TokenTypeRepository")
 */
class TokenType
{
    const USER = [
        'ACTIVATION' => 'profile.activation',
        'REQUEST_RESET_PASSWORD' => 'profile.request-reset-password'
    ];

    use MasterData;

    /**
     * @var Token[] $tokens
     * @ORM\OneToMany(targetEntity="EryseClient\Entity\Client\Token\Token", mappedBy="type")
     */
    protected $tokens;
}
