<?php

namespace Style34\Traits;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait
 * @package Style34\Service\Traits
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