<?php

namespace EryseClient\Utility;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait
 * @package EryseClient\Service\Utility
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