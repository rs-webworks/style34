<?php


namespace EryseClient\Repository\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\User\Settings;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SettingsRepository
 * @package EryseClient\Repository\Profile
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