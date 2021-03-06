<?php declare(strict_types=1);

namespace EryseClient\Common\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ExpiresAt
 *
 */
trait ExpiresAt
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    private DateTime $expiresAt;

    /**
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param DateTime $expiresAt
     */
    public function setExpiresAt(DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}
