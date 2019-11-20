<?php declare(strict_types=1);

namespace EryseClient\Client\ProfileSettings\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Client\ProfileSettings\Entity\ProfileSettings;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\FindByUserTrait;
use EryseClient\Server\User\Entity\User;

/**
 * Class SettingsRepository
 * @method ProfileSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileSettings|null findByUser(User $user)
 */
class ProfileSettingsRepository extends AbstractRepository
{
    use FindByUserTrait;

    /**
     * RoleRepository constructor
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileSettings::class);
    }
}
