<?php declare(strict_types=1);

namespace EryseClient\Server\User\Settings\Repository;

use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\FindByUserTrait;
use EryseClient\Server\User\Settings\Entity\SettingsEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ServiceSettingsRepository
 * @method SettingsEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsEntity|null findByUser(UserInterface $user)
 */
class SettingsRepository extends AbstractRepository
{
    use FindByUserTrait;

    /**
     * ServerSettingsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsEntity::class);
    }
}
