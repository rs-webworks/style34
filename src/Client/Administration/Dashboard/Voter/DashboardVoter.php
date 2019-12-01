<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Dashboard\Voter;

use EryseClient\Client\Administration\Voter\AdminControllerVoter;

/**
 * Class DashboardVoter
 *
 * @package EryseClient\Client\Administration\Dashboard\Voter
 */
class DashboardVoter extends AdminControllerVoter
{
    const DASHBOARD = "dashboard";

    const TARGETS = [
        self::DASHBOARD
    ];
}
