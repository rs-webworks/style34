<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Dashboard\Voter;

use EryseClient\Client\Administration\Voter\AdminControllerVoter;

/**
 * Class DashboardVoter
 *
 *
 */
class DashboardVoter extends AdminControllerVoter
{
    const DASHBOARD = "dashboard";

    const TARGETS = [
        self::DASHBOARD
    ];
}
