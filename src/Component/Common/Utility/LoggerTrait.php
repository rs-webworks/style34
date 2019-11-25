<?php declare(strict_types=1);
namespace EryseClient\Component\Common\Utility;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait
 * @package EryseClient\Service\Utility
 * @deprecated Use \Psr\Log\LoggerAwareTrait
 */
trait LoggerTrait
{

    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
