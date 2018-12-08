<?php

namespace Style34\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
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

    /** @var TokenService $tokenService */
    protected $tokenService;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenService = $tokenService;
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
        $this->em->beginTransaction();

        // Get default profile role
        $role = $this->em->getRepository(Role::class)->findOneBy(array('name' => Role::INACTIVE));
        $profile->setRole($role);

        // Encode password
        $password = $this->passwordEncoder->encodePassword($profile, $profile->getPlainPassword());
        $profile->setPassword($password);

        // Save entity
        $profile->setCreatedAt(new \DateTime());
        $this->em->persist($profile);

        // Create token
        $token = new Token();
        $token->setHash($this->tokenService->generateActivationToken());
        $token->setProfile($profile);
        $createdAt = new \DateTime();
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($this->tokenService->createExpirationDateTime($createdAt, Token::EXPIRY_WEEK * 2));
        $token->setType(TokenType::PROFILE['ACTIVATION']);

        $this->em->persist($token);
        $this->em->flush();

        $this->em->commit();

        return true;
    }
}