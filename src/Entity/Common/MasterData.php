<?php declare(strict_types=1);

namespace EryseClient\Entity\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait MasterData
 * @package EryseClient\Entity
 */
trait MasterData
{
    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string")
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
