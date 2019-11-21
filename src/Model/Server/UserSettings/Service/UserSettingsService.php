<?php declare(strict_types=1);

namespace EryseClient\Model\Server\UserSettings\Service;

use EryseClient\Model\Common\Service\AbstractService;
use EryseClient\Model\Server\UserSettings\Repository\UserSettingsRepository;

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
