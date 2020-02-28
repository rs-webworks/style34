<?php declare(strict_types=1);

namespace EryseClient\Common\Voter;

use EryseClient\Client\Profile\Role\Service\RoleService as ProfileRoleService;
use EryseClient\Server\User\Role\Service\RoleService as UserRoleService;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CrudVoter
 *
 *
 */
abstract class ControllerVoter extends Voter
{
    public const VIEW = "view";

    public const ACTIONS = [
        self::VIEW
    ];

    /** @var UserRoleService */
    protected $userRoleService;

    /** @var ProfileRoleService */
    protected $profileRoleService;

    /**
     * CrudVoter constructor.
     *
     * @param UserRoleService $userRoleService
     * @param ProfileRoleService $profileRoleService
     */
    public function __construct(UserRoleService $userRoleService, ProfileRoleService $profileRoleService)
    {
        $this->userRoleService = $userRoleService;
        $this->profileRoleService = $profileRoleService;
    }
}
