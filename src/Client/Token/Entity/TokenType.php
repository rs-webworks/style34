<?php declare(strict_types=1);

namespace EryseClient\Client\Token\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\MasterData;

/**
 * Class TokenType
 * @package EryseClient\Entity\Client\Token
 * @ORM\Table(name="token_types")
 * @ORM\Entity(repositoryClass="EryseClient\Client\Token\Repository\TokenTypeRepository")
 */
class TokenType implements ClientEntity
{
    const USER = [
        'ACTIVATION' => 'profile.activation',
        'REQUEST_RESET_PASSWORD' => 'profile.request-reset-password'
    ];

    use MasterData;

    /**
     * @var Token[] $tokens
     * @ORM\OneToMany(targetEntity="EryseClient\Client\Token\Entity\Token", mappedBy="type")
     */
    protected $tokens;
}