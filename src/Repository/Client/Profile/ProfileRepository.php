<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\Profile;

use Doctrine\ORM\NonUniqueResultException;
use EryseClient\Entity\Client\Profile\Profile;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\AbstractRepository;
use EryseClient\Repository\Server\User\UserRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
     * @param RegistryInterface $registry
     * @param UserRepository $userRepository
     */
    public function __construct(RegistryInterface $registry, UserRepository $userRepository)
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
