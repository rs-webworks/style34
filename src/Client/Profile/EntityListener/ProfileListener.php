<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\EntityListener;

use EryseClient\Client\Profile\Entity\Entity\Profile;
use EryseClient\Client\ProfileRole\Repository\ProfileRoleRepository;

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
