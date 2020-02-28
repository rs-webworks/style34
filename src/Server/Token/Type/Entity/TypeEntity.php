<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Type\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\MasterData;
use EryseClient\Common\Token\TokenInterface;

/**
 * Class TokenType
 *
 * @ORM\Table(name="token_types")
 * @ORM\Entity(repositoryClass="EryseClient\Server\Token\Type\Repository\TypeRepository")
 */
class TypeEntity implements ClientEntity
{
    const USER = [
        'ACTIVATION' => 'profile.activation',
        'REQUEST_RESET_PASSWORD' => 'profile.request-reset-password'
    ];

    use MasterData;

    /**
     * @var TokenInterface[] $tokens
     * @ORM\OneToMany(targetEntity="EryseClient\Server\Token\Entity\TokenEntity", mappedBy="type")
     */
    protected $tokens;
}
