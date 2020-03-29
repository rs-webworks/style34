<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Settings\Repository;

use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Client\Profile\Settings\Entity\SettingsEntity;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\FindByUserTrait;
use EryseClient\Server\User\Entity\UserEntity;

/**
 * Class SettingsRepository
 * @method SettingsEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsEntity|null findByUser(UserEntity $user)
 */
class SettingsRepository extends AbstractRepository
{
    use FindByUserTrait;

    /**
     * RoleRepository constructor
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsEntity::class);
    }
}
