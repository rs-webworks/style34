<?php declare(strict_types=1);

namespace EryseClient\EntityListener\Client\User;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EryseClient\Entity\Client\User\Role;
use EryseClient\Repository\Server\User\UserRepository;

class RoleListener
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function postLoad(Role $role, LifecycleEventArgs $args)
    {
        $role->setUsers($this->userRepository->findByRole($role->getName()));
    }

}