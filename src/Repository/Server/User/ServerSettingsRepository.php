<?php declare(strict_types=1);

namespace EryseClient\Repository\Server\User;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Entity\Server\User\ServerSettings;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\AbstractRepository;
use EryseClient\Utility\FindByUserTrait;

/**
 * Class ServiceSettingsRepository
 * @method ServerSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerSettings|null findByUser(User $user)
 */
class ServerSettingsRepository extends AbstractRepository
{
    use FindByUserTrait;

    /**
     * ServerSettingsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerSettings::class);
    }
}
