<?php declare(strict_types=1);

namespace EryseClient\Model\Client\ProfileRole\EntityListener;

use EryseClient\Model\Client\ProfileRole\Entity\ProfileRole;
use EryseClient\Model\Server\User\Repository\UserRepository;

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
