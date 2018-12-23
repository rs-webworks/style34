<?php
namespace Style34\Entity\Token;

use Doctrine\ORM\Mapping as ORM;
use Style34\Entity\MasterData;

/**
 * Class TokenType
 * @package Style34\Entity\Token
 * @ORM\Entity(repositoryClass="Style34\Repository\Token\TokenTypeRepository")
 */
class TokenType
{
    const PROFILE = array(
        'ACTIVATION' => 'profile.activation',
        'REQUEST_RESET_PASSWORD' => 'profile.request-reset-password'
    );

    use MasterData;

    /**
     * @var Token[] $tokens
     * @ORM\OneToMany(targetEntity="Style34\Entity\Token\Token", mappedBy="type")
     */
    protected $tokens;
}