<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\Profile;

use EryseClient\Entity\Client\Profile\Profile;
use EryseClient\Repository\AbstractRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ProfileRepository
 * @package EryseClient\Repository\Profile
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 */
class ProfileRepository extends AbstractRepository
{
    /**
     * ProfileRepository constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Profile::class);
    }
}
