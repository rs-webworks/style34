<?php
namespace eRyseClient\Entity\Token;

use Doctrine\ORM\Mapping as ORM;
use eRyseClient\Entity\MasterData;

/**
 * Class TokenType
 * @package eRyseClient\Entity\Token
 * @ORM\Entity(repositoryClass="eRyseClient\Repository\Token\TokenTypeRepository")
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
     * @ORM\OneToMany(targetEntity="eRyseClient\Entity\Token\Token", mappedBy="type")
     */
    protected $tokens;
}