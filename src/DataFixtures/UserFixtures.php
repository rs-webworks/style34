<?php declare(strict_types=1);

namespace EryseClient\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use EryseClient\Entity\Client\User\Role;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\Server\User\UserRepository;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileFixtures
 * @package EryseClient\DataFixtures
 */
class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserRepository */
    private $userRepository;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i <= 100; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setCreatedAt($faker->dateTime("now"));
            $user->setPassword($this->passwordEncoder->encodePassword($user, $faker->password));
            $user->setRole(Role::VERIFIED);

            $user->setLastIp($faker->ipv4);
            $user->setRegisteredAs(serialize([$user->getUsername(), $user->getEmail()]));

            $this->userRepository->saveNew($user);
        }
    }

}
