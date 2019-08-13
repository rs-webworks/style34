<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Client\User\Role;
use EryseClient\Entity\Server\User\User;
use EryseClient\Utility\FindByUserTrait;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RoleRepository
 * @package EryseClient\Repository\Client\User
 */
class RoleRepository extends ServiceEntityRepository
{
    use SaveEntityTrait;

    /**
     * RoleRepository constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Role::class);
    }
}