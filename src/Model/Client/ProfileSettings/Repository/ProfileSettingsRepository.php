<?php declare(strict_types=1);

namespace EryseClient\Model\Client\ProfileSettings\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Model\Client\ProfileSettings\Entity\ProfileSettings;
use EryseClient\Model\Common\Repository\AbstractRepository;
use EryseClient\Model\Server\User\Entity\FindByUserTrait;
use EryseClient\Model\Server\User\Entity\User;

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
