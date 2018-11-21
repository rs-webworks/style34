<?php


namespace Style34\Repository\Address;

use Style34\Entity\Profile\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class StateRepository
 * @package Style34\Repository\Address
 */
class StateRepository extends ServiceEntityRepository
{
    /**
     * RoleRepository constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Role::class);
    }
}