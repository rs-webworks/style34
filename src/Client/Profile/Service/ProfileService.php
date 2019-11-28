<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Service;

use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Client\ProfileRole\Service\ProfileRoleService;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Utility\EryseUserAwareTrait;
use EryseClient\Server\UserRole\Entity\UserRole;
use EryseClient\Server\UserRole\Service\UserRoleService;
use Symfony\Component\Security\Core\Security;

/**
 * Class ProfileService
 *
 * @package EryseClient\Client\Profile\Service
 */
class ProfileService extends AbstractService
{
    use EryseUserAwareTrait;

    /** @var UserRoleService */
    private $userRoleService;

    /** @var Security */
    private $security;

    /** @var ProfileRoleService */
    private $profileService;

    /**
     * ProfileService constructor.
     *
     * @param UserRoleService $userRoleService
     * @param ProfileRoleService $profileRoleService
     * @param Security $security
     */
    public function __construct(
        UserRoleService $userRoleService,
        ProfileRoleService $profileRoleService,
        Security $security
    ) {
        $this->userRoleService = $userRoleService;
        $this->security = $security;
        $this->profileService = $profileRoleService;
    }

    /**
     * @param Profile $profile
     *
     * @return bool
     */
    public function isDisplayable(Profile $profile): bool
    {
        $displayable = true;

        if ($this->userRoleService->isRoleBlocked($profile->getUser()->getRole())) {
            $displayable = false;
        }

        if ($this->profileService->isRoleBlocked($profile->getUser()->getRole())) {
            $displayable = false;
        }

        if ($this->security->isGranted(UserRole::ADMIN)) {
            $displayable = true;
        }

        return $displayable;
    }
}
