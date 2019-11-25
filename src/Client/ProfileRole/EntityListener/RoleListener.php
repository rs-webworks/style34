<?php declare(strict_types=1);

namespace EryseClient\Client\ProfileRole\EntityListener;

use EryseClient\Client\ProfileRole\Entity\ProfileRole;
use EryseClient\Server\User\Repository\UserRepository;

/**
 * TODO: FIX refactor
 * Class RoleListener
 * @package EryseClient\EntityListener\Client\User
 */
class RoleListener
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * RoleListener constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ProfileRole $role
     */
    public function postLoad(ProfileRole $role)
    {
        $role->setProfiles($this->userRepository->findByRole($role->getName()));
    }
}
