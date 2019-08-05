<?php declare(strict_types=1);
namespace EryseClient\Entity\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property-read int $id
 */
trait Identifier
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    protected $id;

    final public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function __clone()
    {
        $this->id = null;
    }

}