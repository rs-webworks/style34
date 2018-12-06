<?php

namespace Style34\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileService
 * @package Style34\Service
 */
class ProfileService extends AbstractService
{
    /** @var EntityManager $em */
    protected $em;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Profile $profile
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function registerNewProfile(Profile $profile)
    {
        // Get default profile role
        $role = $this->em->getRepository(Role::class)->findOneBy(array('name' => Role::INACTIVE));
        $profile->setRole($role);

        // Encode password
        $password = $this->passwordEncoder->encodePassword($profile, $profile->getPlainPassword());
        $profile->setPassword($password);

        // Save entity
        $profile->setCreatedAt(new \DateTime());
        $this->em->persist($profile);
        $this->em->flush();

        return true;
    }
}