<?php declare(strict_types=1);
namespace EryseClient\Entity\Common;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ExpiresAt
 * @package EryseClient\Entity
 */
trait ExpiresAt
{

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    private $expiresAt;

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

}