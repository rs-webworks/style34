<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ProfileRepository
 * @package EryseClient\Repository\Profile
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 */
class ProfileRepository extends AbstractRepository implements UserLoaderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ProfileRepository constructor
     * @param ManagerRegistry $registry
     * @param UserRepository $userRepository
     */
    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, Profile::class);
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     * @return UserInterface|void|null
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->userRepository->loadUserByUsername($username);
        return $this->find($user->getProfileId());
    }
}
