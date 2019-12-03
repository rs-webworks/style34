<?php declare(strict_types=1);

namespace EryseClient\Common\Utility;

use EryseClient\Client\Application\Service\Application;

/**
 * Trait EryseAppAwareTrait
 *
 * @package EryseClient\Common\Utility
 */
trait EryseAppAwareTrait
{

    /** @var Application */
    protected $clientApp;

    /**
     * @required
     * @param Application $clientApp
     */
    public function setClientApp(Application $clientApp): void
    {
        $this->clientApp = $clientApp;
    }
}
