<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Client\User\ClientSettings;
use EryseClient\Entity\Server\User\User;
use EryseClient\Utility\FindByUserTrait;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SettingsRepository
 * @method ClientSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientSettings|null findByUser(User $user)
 */
class ClientSettingsRepository extends ServiceEntityRepository
{
    use SaveEntityTrait;
    use FindByUserTrait;

    /**
     * RoleRepository constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClientSettings::class);
    }
}