<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Repository;

use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Repository\UserRepository;

/**
 * Class ProfileRepository
 *
 *
 * @method ProfileEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileEntity|null find($id, $lockMode = null, $lockVersion = null)
 */
class ProfileRepository extends AbstractRepository
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * ProfileRepository constructor
     *
     * @param ManagerRegistry $registry
     * @param UserRepository $userRepository
     */
    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, ProfileEntity::class);
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     *
     * @return ProfileEntity
     */
    public function findOneByUserId(int $userId): ProfileEntity
    {
        return $this->findOneBy(['userId' => $userId]);
    }

    /**
     * @param string $username
     *
     * @return ProfileEntity
     */
    public function findOneByUsername(string $username): ProfileEntity
    {
        $user = $this->userRepository->findOneByUsername($username);
        $profile = $this->findOneByUserId($user->getId());
        $profile->setUser($user);

        return $profile;
    }
}
