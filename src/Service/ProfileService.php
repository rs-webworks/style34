<?php

namespace eRyseClient\Service;

use eRyseClient\Entity\Profile\Profile;
use eRyseClient\Entity\Profile\Role;
use eRyseClient\Entity\Profile\Settings;
use eRyseClient\Entity\Token\RememberMeToken;
use eRyseClient\Entity\Token\Token;
use eRyseClient\Entity\Token\TokenType;
use eRyseClient\Exception\Profile\ActivationException;
use eRyseClient\Exception\Token\ExpiredTokenException;
use eRyseClient\Exception\Token\InvalidTokenException;
use eRyseClient\Kernel;
use eRyseClient\Repository\Token\TokenRepository;
use eRyseClient\Repository\Token\TokenTypeRepository;
use eRyseClient\Traits\EntityManagerTrait;
use eRyseClient\Traits\LoggerTrait;
use eRyseClient\Traits\TranslatorTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileService
 * @package eRyseClient\Service
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
        $profile->setTrustedTokenVersion($profile->getTrustedTokenVersion() + 1);

        $this->em->persist($settings);
        $this->em->persist($profile);
        $this->em->flush();
    }

    /**
     * @param Profile $profile
     */
    public function forgetDevices(Profile $profile)
    {
        $profile->setTrustedTokenVersion($profile->getTrustedTokenVersion() + 1);
        $this->em->persist($profile);
        $this->em->flush();
    }

    /**
     * @param Profile $profile
     * @return bool
     */
    public function hasRememberMeToken(Profile $profile)
    {
        $token = $this->em->getRepository(RememberMeToken::class)->findOneBy(array(
            'username' => $profile->getUsername()
        ));

        return $token ? true : false;
    }

    /**
     * @param Profile $profile
     */
    public function logoutEverywhere(Profile $profile)
    {
        $profile;
    }

}