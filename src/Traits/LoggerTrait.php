<?php

namespace eRyseClient\Traits;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait
 * @package eRyseClient\Service\Traits
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