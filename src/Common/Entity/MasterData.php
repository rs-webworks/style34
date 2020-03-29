<?php declare(strict_types=1);

namespace EryseClient\Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait MasterData
 *
 */
trait MasterData
{
    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $name;

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
