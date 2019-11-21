<?php declare(strict_types=1);

namespace EryseClient\Model\Server\User\EntityListener;

use EryseClient\Model\Client\ProfileRole\Repository\ProfileRoleRepository;
use EryseClient\Model\Server\User\Entity\User;

/**
 * Class UserListener
 * @package EryseClient\EntityListener\Client\User
 */
class UserListener
{
    /** @var ProfileRoleRepository */
    private $roleRepository;

    /**
     * UserListener constructor.
     * @param ProfileRoleRepository $roleRepository
     */
    public function __construct(ProfileRoleRepository $roleRepository)
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
