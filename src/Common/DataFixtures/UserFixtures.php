<?php declare(strict_types=1);

namespace EryseClient\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Role\Entity\RoleEntity as ProfileRole;
use EryseClient\Client\Profile\Role\Repository\RoleRepository;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Role\Entity\RoleEntity as UserRole;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileFixtures
 *
 */
class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private UserPasswordEncoderInterface $passwordEncoder;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var ProfileRepository */
    private ProfileRepository $profileRepository;

    /** @var RoleRepository */
    private RoleRepository $profileRoleRepository;

    /** @var Generator */
    private Generator $faker;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param ProfileRepository $profileRepository
     * @param RoleRepository $profileRoleRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        ProfileRepository $profileRepository,
        RoleRepository $profileRoleRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->profileRoleRepository = $profileRoleRepository;

        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function load(ObjectManager $manager): void
    {
        $faker = $this->faker;

        for ($i = 0; $i <= 100; $i++) {
            $user = new UserEntity();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setCreatedAt($faker->dateTime('now'));
            $user->setPassword($this->passwordEncoder->encodePassword($user, $faker->password));

            $roles = [UserRole::INACTIVE, UserRole::VERIFIED];
            $randomRole = array_rand($roles, 1);

            $user->setRole($roles[$randomRole]);

            $user->setLastIp($faker->ipv4);
            $user->setRegisteredAs(serialize([$user->getUsername(), $user->getEmail()]));

            $this->userRepository->saveNew($user);

            $profile = new ProfileEntity();
            $profile->setBirthdate($faker->dateTimeThisCentury());
            $profile->setCity($faker->city);

            $roles = [ProfileRole::INACTIVE, ProfileRole::BANNED, ProfileRole::DELETED, ProfileRole::MEMBER];
            $randomRole = array_rand($roles, 1);

            $role = $this->profileRoleRepository->findOneByName($roles[$randomRole]);

            $profile->setRole($role);
            $profile->setOccupation($faker->jobTitle);
            $profile->setState($faker->state);
            $profile->setUserId($user->getId());

            $this->profileRepository->save($profile);
        }
    }
}
