<?php declare(strict_types=1);

namespace EryseClient\Repository\Server\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Server\User\ServerSettings;
use EryseClient\Entity\Server\User\User;
use EryseClient\Utility\FindByUserTrait;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ServiceSettingsRepository
 * @method ServerSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerSettings|null findByUser(User $user)
 */
class ServerSettingsRepository extends ServiceEntityRepository
{
    use SaveEntityTrait;
    use FindByUserTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ServerSettings::class);
    }

}