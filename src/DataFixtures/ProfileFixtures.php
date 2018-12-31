<?php

namespace eRyseClient\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileFixtures
 * @package eRyseClient\DataFixtures
 */
class ProfileFixtures extends Fixture {
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * ProfileFixtures constructor
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

    }
}
