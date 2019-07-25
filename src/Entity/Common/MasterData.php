<?php

namespace EryseClient\Entity\Common;

/**
 * Trait MasterData
 * @package EryseClient\Entity
 */
trait MasterData
{
    use Identifier;

    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

}