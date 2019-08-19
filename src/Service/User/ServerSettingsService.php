<?php declare(strict_types=1);

namespace EryseClient\Service\User;

use EryseClient\Repository\Server\User\ServerSettingsRepository;
use EryseClient\Service\AbstractService;

/**
 * Class ServerSettingsService
 * @package EryseClient\Service\User
 */
class ServerSettingsService extends AbstractService
{

    /** @var ServerSettingsRepository */
    private $serverSettingsRepository;

    /**
     * ServerSettingsService constructor.
     * @param ServerSettingsRepository $serverSettingsRepository
     */
    public function __construct(ServerSettingsRepository $serverSettingsRepository)
    {
        $this->serverSettingsRepository = $serverSettingsRepository;
    }
}
