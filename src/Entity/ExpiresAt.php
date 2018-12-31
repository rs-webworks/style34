<?php

namespace eRyseClient\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ExpiresAt
 * @package eRyseClient\Entity
 */
trait ExpiresAt
{

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $expiresAt;

    /**
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

}