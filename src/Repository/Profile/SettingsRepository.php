<?php


namespace Style34\Repository\Profile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Settings;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SettingsRepository
 * @package Style34\Repository\Profile
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