<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\MasterData;
use EryseClient\Common\Token\TokenInterface;

/**
 * Class TokenType
 * @package EryseClient\Entity\Client\Token
 * @ORM\Table(name="token_types")
 * @ORM\Entity(repositoryClass="EryseClient\Server\Token\Repository\TokenTypeRepository")
 */
class TokenType implements ClientEntity
{
    const USER = [
        'ACTIVATION' => 'profile.activation',
        'REQUEST_RESET_PASSWORD' => 'profile.request-reset-password'
    ];

    use MasterData;

    /**
     * @var TokenInterface[] $tokens
     * @ORM\OneToMany(targetEntity="EryseClient\Server\Token\Entity\Token", mappedBy="type")
     */
    protected $tokens;
}
