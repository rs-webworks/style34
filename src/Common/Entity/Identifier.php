<?php declare(strict_types=1);

namespace EryseClient\Common\Entity;

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

    /**
     * @return int
     */
    final public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     *
     */
    public function __clone()
    {
        $this->id = null;
    }
}
