<?php

namespace EryseClient\Entity\Common;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait DeletedAt
 * @package EryseClient\Entity
 */
trait DeletedAt
{

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $deletedAt;

    public function getDeletedAt(): DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

}