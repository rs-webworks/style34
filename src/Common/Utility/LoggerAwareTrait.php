<?php declare(strict_types=1);

namespace EryseClient\Common\Utility;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerAwareTrait
 */
trait LoggerAwareTrait
{
    use \Psr\Log\LoggerAwareTrait;

    /**
     * @required
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }

}
