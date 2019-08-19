<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Client\User\Role;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RoleRepository
 * @package EryseClient\Repository\Client\User
 * @method Role|null findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
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
