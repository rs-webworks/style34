<?php

namespace Style34\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait MasterData
 * @package Style34\Entity
 */
trait MasterData
{

    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }


}