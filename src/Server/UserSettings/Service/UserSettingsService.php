<?php declare(strict_types=1);

namespace EryseClient\Server\UserSettings\Service;

use EryseClient\Common\Service\AbstractService;
use EryseClient\Server\UserSettings\Repository\UserSettingsRepository;

/**
 * Class ServerSettingsService
 * @package EryseClient\Service\User
 */
class UserSettingsService extends AbstractService
{

    /** @var UserSettingsRepository */
    private $serverSettingsRepository;

    /**
     * ServerSettingsService constructor.
     * @param UserSettingsRepository $serverSettingsRepository
     */
    public function __construct(UserSettingsRepository $serverSettingsRepository)
    {
        $this->serverSettingsRepository = $serverSettingsRepository;
    }
}
