<?php


namespace eRyseClient\Repository\Profile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use eRyseClient\Entity\Profile\Profile;
use eRyseClient\Entity\Profile\Settings;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SettingsRepository
 * @package eRyseClient\Repository\Profile
 */
class SettingsRepository extends ServiceEntityRepository
{
    /**
     * RoleRepository constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Settings::class);
    }
}