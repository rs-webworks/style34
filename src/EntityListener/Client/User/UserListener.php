<?php declare(strict_types=1);

namespace EryseClient\EntityListener\Client\User;

use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\Client\User\RoleRepository;

/**
 * Class UserListener
 * @package EryseClient\EntityListener\Client\User
 */
class UserListener
{
    /** @var RoleRepository */
    private $roleRepository;

    /**
     * UserListener constructor.
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param User $user
     */
    public function postLoad(User $user)
    {
        $user->setRoleEntity($this->roleRepository->findOneBy(["name" => $user->getRole()]));
    }
}
