<?php


namespace eRyseClient\Repository\Profile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use eRyseClient\Entity\Profile\Role;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RoleRepository
 * @package eRyseClient\Repository\Profile
 */
class RoleRepository extends ServiceEntityRepository
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