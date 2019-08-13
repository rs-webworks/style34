<?php declare(strict_types=1);

namespace EryseClient\Service\User;

use EryseClient\Repository\Server\User\ServerSettingsRepository;
use EryseClient\Service\AbstractService;

class ServerSettingsService extends AbstractService
{

    /** @var ServerSettingsRepository  */
    private $serverSettingsRepository;

    public function __construct(ServerSettingsRepository $serverSettingsRepository)
    {
        $this->serverSettingsRepository = $serverSettingsRepository;
    }

}