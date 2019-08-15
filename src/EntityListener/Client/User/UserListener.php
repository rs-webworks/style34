<?php declare(strict_types=1);

namespace EryseClient\EntityListener\Client\User;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\Client\User\RoleRepository;

class UserListener
{
    /** @var RoleRepository */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function postLoad(User $user, LifecycleEventArgs $args)
    {
        $user->setRoleEntity($this->roleRepository->findOneBy(["name" => $user->getRole()]));
    }

}