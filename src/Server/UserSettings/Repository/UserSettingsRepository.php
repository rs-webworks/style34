<?php declare(strict_types=1);

namespace EryseClient\Server\UserSettings\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\FindByUserTrait;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\UserSettings\Entity\UserSettings;

/**
 * Class ServiceSettingsRepository
 * @method UserSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSettings|null findByUser(User $user)
 */
class UserSettingsRepository extends AbstractRepository
{
    use FindByUserTrait;

    /**
     * ServerSettingsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSettings::class);
    }
}
