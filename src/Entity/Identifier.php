<?php

namespace AppBundle\Entity;

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
    private $id;

	/**
	 * @return int
	 */
    final public function getId(): int
    {
        return $this->id;
    }

	/**
	 *
	 */
    public function __clone()
    {
        $this->id = NULL;
    }

}