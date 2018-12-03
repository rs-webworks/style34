<?php
namespace Style34\Entity\Token;

use Doctrine\ORM\Mapping as ORM;
use Style34\Entity\MasterData;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TokenType
 * @package Style34\Entity\Token
 * @ORM\Entity
 */
class TokenType
{
    const REGISTRATION = array(
        'ACTIVATION' => 'registration.activation'
    );

    use MasterData;

    /**
     * @var Token[] $tokens
     * @ORM\OneToMany(targetEntity="Style34\Entity\Token\Token", mappedBy="type")
     */
    protected $tokens;
}