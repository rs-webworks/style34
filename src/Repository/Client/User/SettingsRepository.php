<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Client\User\Settings;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SettingsRepository
 * @package EryseClient\Repository\Profile
 * @method Settings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Settings|null find($id, $lockMode = null, $lockVersion = null)
 */
class SettingsRepository extends ServiceEntityRepository
{
    use SaveEntityTrait;

    /**
     * RoleRepository constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Settings::class);
    }
}