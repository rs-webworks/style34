<?php declare(strict_types=1);

namespace EryseClient\Server\User\Settings\Service;

use EryseClient\Common\Service\AbstractService;
use EryseClient\Server\User\Settings\Repository\SettingsRepository;

/**
 * Class ServerSettingsService
 *
 */
class UserSettingsService extends AbstractService
{

    /** @var SettingsRepository */
    private $serverSettingsRepository;

    /**
     * ServerSettingsService constructor.
     *
     * @param SettingsRepository $serverSettingsRepository
     */
    public function __construct(SettingsRepository $serverSettingsRepository)
    {
        $this->serverSettingsRepository = $serverSettingsRepository;
    }
}
