<?php declare(strict_types=1);

namespace EryseClient\Model\Client\Profile\EntityListener;

use EryseClient\Model\Client\Profile\Entity\Profile;
use EryseClient\Model\Client\ProfileRole\Repository\ProfileRoleRepository;
use EryseClient\Model\Server\User\Entity\User;
use EryseClient\Model\Server\UserRole\Repository\UserRoleRepository;

/**
 * Class UserListener
 * @package EryseClient\EntityListener\Client\User
 */
class ProfileListener
{
    /** @var ProfileRoleRepository */
    private $profileRoleRepository;

    /**
     * UserListener constructor.
     * @param ProfileRoleRepository $profileRoleRepository
     */
    public function __construct(ProfileRoleRepository $profileRoleRepository)
    {
        $this->profileRoleRepository = $profileRoleRepository;
    }

    /**
     * @param Profile $user
     */
    public function postLoad(Profile $user)
    {
        $user->setRole($this->profileRoleRepository->findOneBy(["name" => $user->getRole()]));
    }
}
