<?php

namespace EryseClient\Entity\Common;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait CreatedAt
 * @package EryseClient\Entity
 */
trait CreatedAt
{

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    private $createdAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}