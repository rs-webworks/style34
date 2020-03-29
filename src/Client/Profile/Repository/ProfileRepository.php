<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Client\Profile\Settings\Entity\SettingsEntity;
use EryseClient\Client\Profile\Settings\Repository\SettingsRepository;
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
     * @var SettingsRepository
     */
    private SettingsRepository $settingsRepository;

    /**
     * ProfileRepository constructor
     *
     * @param ManagerRegistry $registry
     * @param UserRepository $userRepository
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        ManagerRegistry $registry,
        UserRepository $userRepository,
        SettingsRepository $settingsRepository
    ) {
        parent::__construct($registry, ProfileEntity::class);
        $this->userRepository = $userRepository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param string $userId
     *
     * @return ProfileEntity
     */
    public function findOneByUserId(string $userId) : ProfileEntity
    {
        return $this->findOneBy(['userId' => $userId]);
    }

    /**
     * @param string $username
     *
     * @return ProfileEntity
     */
    public function findOneByUsername(string $username) : ProfileEntity
    {
        $user = $this->userRepository->findOneByUsername($username);
        $profile = $this->findOneByUserId($user->getId());
        $profile->setUser($user);

        return $profile;
    }

    /**
     * @param ProfileEntity $profileEntity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveAndCreateSettings(ProfileEntity $profileEntity): void
    {
        $this->save($profileEntity);
        $this->settingsRepository->save(new SettingsEntity($profileEntity));
    }
}
