<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Service;

use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Client\Profile\Role\Service\RoleService;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Utility\EryseUserAwareTrait;
use EryseClient\Server\User\Role\Service\RoleService as UserRoleService;

/**
 * Class ProfileService
 */
class ProfileService extends AbstractService
{
    use EryseUserAwareTrait;

    /** @var UserRoleService */
    private UserRoleService $userRoleService;

    /** @var RoleService */
    private RoleService $profileService;

    /**
     * ProfileService constructor.
     *
     * @param UserRoleService $userRoleService
     * @param RoleService $profileRoleService
     */
    public function __construct(
        UserRoleService $userRoleService,
        RoleService $profileRoleService
    ) {
        $this->userRoleService = $userRoleService;
        $this->profileService = $profileRoleService;
    }

    /**
     * @param ProfileEntity $profile
     *
     * @return bool
     */
    public function isDisplayable(ProfileEntity $profile): bool
    {
        $displayable = true;

        if ($this->userRoleService->isRoleBlocked($profile->getUser()->getRole())) {
            $displayable = false;
        }

        if ($this->profileService->isRoleBlocked($profile->getUser()->getRole())) {
            $displayable = false;
        }

        return $displayable;
    }
}
