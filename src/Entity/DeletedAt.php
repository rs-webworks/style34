<?php

namespace EryseClient\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait DeletedAt
 * @package EryseClient\Entity
 */
trait DeletedAt
{

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @return \DateTime
     */
    public function getDeletedAt(): \DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

}