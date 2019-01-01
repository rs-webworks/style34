<?php

namespace EryseClient\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait CreatedAt
 * @package EryseClient\Entity
 */
trait CreatedAt
{

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}