<?php

namespace Style34\Service;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Style34\Entity\Profile\Settings;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Style34\Exception\Profile\ActivationException;
use Style34\Exception\Token\ExpiredTokenException;
use Style34\Exception\Token\InvalidTokenException;
use Style34\Kernel;
use Style34\Repository\Token\TokenRepository;
use Style34\Repository\Token\TokenTypeRepository;
use Style34\Traits\EntityManagerTrait;
use Style34\Traits\LoggerTrait;
use Style34\Traits\TranslatorTrait;
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

    /** @var TokenTypeRepository $tokenTypeRepository */
    private $tokenTypeRepository;

    /** @var TokenRepository $tokenRepository */
    private $tokenRepository;

    /** @var CryptService $cryptService */
    private $cryptService;

    /**
     * ProfileService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @param TokenTypeRepository $tokenTypeRepository
     * @param TokenRepository $tokenRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService,
        MailService $mailService,
        TokenTypeRepository $tokenTypeRepository,
        TokenRepository $tokenRepository,
        CryptService $cryptService
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenService = $tokenService;
        $this->mailService = $mailService;
        $this->tokenTypeRepository = $tokenTypeRepository;
        $this->tokenRepository = $tokenRepository;
        $this->cryptService = $cryptService;
    }

    /**
     * @param Profile $profile
     * @param string $lastIp
     * @return Profile
     * @throws \Exception
     */
    public function prepareNewProfile(Profile $profile, string $lastIp = '127.0.0.1')
    {
        // Get default profile role
        $profile->addRole(Role::INACTIVE);

        // Encode password
        $password = $this->passwordEncoder->encodePassword($profile, $profile->getPlainPassword());
        $profile->setPassword($password);

        // Set defaults
        $profile->setCreatedAt(new \DateTime());
        $profile->setLastIp($lastIp);
        $profile->setRegisteredAs(serialize(array($profile->getUsername(), $profile->getEmail())));
        $profile->setSettings(new Settings());

        return $profile;
    }

    /**
     * @param Profile $profile
     * @param Token|null $token
     * @return Profile|null
     * @throws ActivationException
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     * @throws \Exception
     */
    public function activateProfile(Profile $profile, Token $token = null): ?Profile
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

        if ($profile->hasRole(Role::INACTIVE)) {
            $profile->addRole(Role::VERIFIED);
            $profile->removeRole(Role::INACTIVE);
            $profile->setActivatedAt(new \DateTime);

            return $profile;
        }

        throw new ActivationException($this->translator->trans('contact-support',
            ['contactmail' => Kernel::CONTACT_MAIL], 'global'));
    }

    /**
     * @param string $newPassword
     * @param Profile $profile
     * @param Token|null $token
     * @return Profile|null
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     * @throws \Exception
     */
    public function updatePassword(string $newPassword, Profile $profile, Token $token = null): ?Profile
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

        $password = $this->passwordEncoder->encodePassword($profile, $newPassword);
        $profile->setPassword($password);

        return $profile;
    }

    /**
     * @return Profile[]|null
     */
    public function getExpiredRegistrations(): ?array
    {
        try {
            $profiles = array();

            /** @var TokenType $tokenType */
            $tokenType = $this->tokenTypeRepository->findOneBy(array(
                'name' => TokenType::PROFILE['ACTIVATION']
            ));

            $expiredTokens = $this->tokenRepository->findExpiredTokens($tokenType);

            /** @var Token $token */
            foreach ($expiredTokens as $token) {
                $profile = $token->getProfile();
                $profiles[] = $profile;
            }

            return $profiles;
        } catch (\Exception $ex) {
            $this->logger->error('profile.expired-registration-purge-failed', [$ex]);
        }

        return null;
    }

    /**
     * @param Profile $profile
     * @param string $secret
     */
    public function enableTwoStepAuth(Profile $profile, string $secret)
    {
        $profile->setGoogleAuthenticatorSecret($secret);
        $settings = $profile->getSettings();

        //        $settings->setGAuthSecret($this->cryptService->encrypt($secret));
        $settings->setTwoStepAuthEnabled(true);

        $this->em->persist($settings);
        $this->em->flush();
    }

    /**
     * @param Profile $profile
     */
    public function disableTwoStepAuth(Profile $profile)
    {
        $settings = $profile->getSettings();

        $settings->setGAuthSecret(null);
        $settings->setTwoStepAuthEnabled(false);

        $this->em->persist($settings);
        $this->em->flush();
    }

    /**
     * @param Profile $profile
     */
    public function forgetDevices(Profile $profile)
    {
        $profile->setTrustedTokenVersion($profile->getTrustedTokenVersion()+1);
        $this->em->persist($profile);
        $this->em->flush();
    }
}