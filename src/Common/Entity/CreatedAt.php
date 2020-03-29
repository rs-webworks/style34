<?php declare(strict_types=1);

namespace EryseClient\Common\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait CreatedAt
 *
 */
trait CreatedAt
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
