<?php

namespace Style34\Service;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Style34\Exception\Profile\ActivationException;
use Style34\Exception\Profile\ProfileException;
use Style34\Exception\Token\ExpiredTokenException;
use Style34\Exception\Token\InvalidTokenException;
use Style34\Kernel;
use Style34\Service\Traits\EntityManagerTrait;
use Style34\Service\Traits\LoggerTrait;
use Style34\Service\Traits\TranslatorTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileService
 * @package Style34\Service
 */
class ProfileService extends AbstractService
{
    use LoggerTrait;
    use EntityManagerTrait;
    use TranslatorTrait;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /** @var TokenService $tokenService */
    protected $tokenService;

    /** @var MailService $mailService */
    protected $mailService;

    /**
     * ProfileService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenService $tokenService
     * @param MailService $mailService
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService,
        MailService $mailService
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenService = $tokenService;
        $this->mailService = $mailService;
    }

    /**
     * @param Profile $profile
     * @param string $lastIp
     * @return bool|mixed
     */
    public function registerNewProfile(Profile $profile, string $lastIp = '127.0.0.1')
    {
        return $this->em->transactional(function () use ($profile, $lastIp) {
            // Get default profile role
            $role = $this->em->getRepository(Role::class)->findOneBy(array('name' => Role::INACTIVE));
            $profile->setRole($role);

            // Encode password
            $password = $this->passwordEncoder->encodePassword($profile, $profile->getPlainPassword());
            $profile->setPassword($password);

            // Save entity
            $profile->setCreatedAt(new \DateTime());
            $profile->setLastIp($lastIp);
            $profile->setRegisteredAs(serialize(array($profile->getUsername(), $profile->getEmail())));
            $this->em->persist($profile);

            // Create token
            $token = new Token();
            $token->setHash($this->tokenService->generateActivationToken());
            $token->setProfile($profile);
            $createdAt = new \DateTime();
            $expiresAt = new \DateTime();
            $token->setCreatedAt($createdAt);
            $token->setExpiresAt($this->tokenService->createExpirationDateTime($expiresAt, Token::EXPIRY_HOUR * 2));
            $token->setType($this->em->getRepository(TokenType::class)->findOneBy(array(
                'name' => TokenType::PROFILE['ACTIVATION']
            )));

            // Send registration email
            $this->mailService->sendActivationMail($profile, $token);

            $this->em->persist($token);
            $this->em->flush();
        });
    }

    /**
     * @param Profile $profile
     * @param Token|null $token
     * @return bool
     * @throws InvalidTokenException
     * @throws ProfileException
     * @throws \Exception
     */
    public function activateProfile(Profile $profile, Token $token = null): bool
    {
        if ($token) {
            if ($this->tokenService->isExpired($token)) {
                throw new ExpiredTokenException($this->translator->trans('activation-expired-token',
                    [], 'profile'));
            }

            if ($token->isInvalid()) {
                throw new InvalidTokenException($this->translator->trans('activation-invalid-token',
                    [], 'profile'));
            }
        }

        if ($profile->getRole()->getName() == Role::INACTIVE) {
            $profile->setRole($this->em->getRepository(Role::class)->findOneBy(array('name' => Role::VERIFIED)));
            $profile->setActivatedAt(new \DateTime);
            $token->setInvalid(true);

            $this->em->persist($profile);
            $this->em->persist($token);

            $this->em->flush();

            return true;
        }

        throw new ActivationException($this->translator->trans('contact-support',
            ['contactmail' => Kernel::CONTACT_MAIL], 'global'));
    }

    /**
     *
     */
    public function purgeExpiredRegistrations()
    {
        try {
            $tokenType = $this->em->getRepository(TokenType::class)->findOneBy(array(
                'name' => TokenType::PROFILE['ACTIVATION']
            ));

            $expiredTokens = $this->em->getRepository(Token::class)->findExpiredTokens($tokenType);

            /** @var Token $token */
            foreach ($expiredTokens as $token) {
                $profile = $token->getProfile();
                $this->em->remove($profile);
            }

            $this->em->flush();
        } catch (\Exception $ex) {
            $this->logger->error('profile.expired-registration-purge-failed', [$ex]);
        }
    }
}