<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use EryseClient\Common\Entity\ClientEntity;

/**
 * Class RememberMeToken
 *
 * @ORM\Entity()
 * @ORM\Table(name="rememberme_token")
 */
class RememberMeTokenEntity implements ClientEntity
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=88, unique=true, nullable=false, options={"fixed" = true})
     */
    protected string $series;

    /**
     * @var string
     * @ORM\Column(type="string", length=88, nullable=false, options={"fixed" = true})
     */
    protected string $value;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false, name="lastused")
     */
    protected DateTime $lastUsed;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected string $class;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=false)
     */
    protected string $username;
}
