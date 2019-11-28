<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Repository\UserRepository;

/**
 * Class ProfileRepository
 *
 * @package EryseClient\Repository\Profile
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 */
class ProfileRepository extends AbstractRepository
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ProfileRepository constructor
     *
     * @param ManagerRegistry $registry
     * @param UserRepository $userRepository
     */
    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, Profile::class);
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     *
     * @return Profile
     */
    public function findOneByUserId(int $userId): Profile
    {
        return $this->findOneBy(["userId" => $userId]);
    }

    /**
     * @param string $username
     *
     * @return Profile
     */
    public function findOneByUsername(string $username): Profile
    {
        $user = $this->userRepository->findOneByUsername($username);
        $profile = $this->findOneByUserId($user->getId());
        $profile->setUser($user);

        return $profile;
    }
}
