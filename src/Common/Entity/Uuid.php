<?php declare(strict_types=1);

namespace EryseClient\Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property-read string $id
 */
trait Uuid
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    protected string $id;

    /**
     * @return string
     */
    final public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     *
     */
    public function __clone()
    {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
