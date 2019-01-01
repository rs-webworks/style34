<?php

namespace EryseClient\Tests\Service;

use EryseClient\Entity\Profile\Profile;
use EryseClient\Entity\Profile\Role;
use EryseClient\Entity\Token\Token;
use EryseClient\Entity\Token\TokenType;
use EryseClient\Exception\User\ActivationException;
use EryseClient\Exception\Token\ExpiredTokenException;
use EryseClient\Exception\Token\InvalidTokenException;
use EryseClient\Repository\Profile\ProfileRepository;
use EryseClient\Repository\Token\TokenTypeRepository;
use EryseClient\Service\ProfileService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileServiceTest
 * @package EryseClient\Tests\Service
 * @covers  \EryseClient\Service\ProfileService
 */
class ProfileServiceTest extends WebTestCase
{
    /** @var ProfileService $profileService */
    protected $profileService;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /** @var ProfileRepository */
    protected $profileRepository;

    /** @var TokenTypeRepository $tokenTypeRepository */
    protected $tokenTypeRepository;

    /** @var array */
    protected $toDeleteEntities;

    /**
     *
     */
    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->profileService = $container->get(ProfileService::class);
        $this->passwordEncoder = $container->get(UserPasswordEncoderInterface::class);
        $this->profileRepository = $container->get(ProfileRepository::class);
        $this->tokenTypeRepository = $container->get(TokenTypeRepository::class);

        $this->profileRepository->removeProfiles(
            $this->profileRepository->findBy(array('email' => 'test@test.com'))
        );
    }

    /**
     * @throws \Exception
     */
    public function testPrepareNewProfile()
    {
        $profile = new Profile();
        $password = 'test_password789456!';
        $profile->setPlainPassword($password);
        $email = 'test@testor.com';
        $profile->setEmail($email);
        $username = 'Testozor';
        $profile->setUsername($username);

        $lastIp = '69.69.69.69';
        $profile = $this->profileService->prepareNewProfile($profile, $lastIp);

        $this->assertTrue($profile->hasRole(Role::USER));
        $this->assertTrue($profile->hasRole(Role::INACTIVE));
        $this->assertFalse($profile->hasRole(Role::ADMIN));
        $this->assertFalse($profile->hasRole(Role::BANNED));
        $this->assertFalse($profile->hasRole(Role::VERIFIED));
        $this->assertFalse($profile->hasRole(Role::MEMBER));
        $this->assertFalse($profile->hasRole(Role::MODERATOR));

        $this->assertTrue($this->passwordEncoder->isPasswordValid($profile, $password));
        $this->assertEquals($lastIp, $profile->getLastIp());
        $this->assertInstanceOf(\DateTime::class, $profile->getCreatedAt());
        $this->assertEquals(serialize(array($username, $email)), $profile->getRegisteredAs());
    }

    /**
     * @throws \Exception
     */
    public function testGetExpiredRegistrations()
    {
        $profiles = $this->profileRepository->findAll();

        $profile = new Profile();
        $username = 'testGetExpiredRegistrations' . rand(1024, 2048);
        $profile->setUsername($username);
        $profile->setEmail('test@test.com');
        $profile->setPassword('empty');
        $profile->setLastIp('127.0.0.1');
        $profile->setRegisteredAs('empty');
        $profile->setCreatedAt(new \DateTime());
        $token = new Token();
        $token->setType($this->tokenTypeRepository->findOneBy(array('name' => TokenType::PROFILE['ACTIVATION'])));
        $token->setProfile($profile);
        $token->setCreatedAt(new \DateTime());
        $token->setExpiresAt(new \DateTime('2012-01-01 00:00:00'));
        $token->setHash('empty');
        $this->profileRepository->save($profile, $token);

        $expired = $this->profileService->getExpiredRegistrations();
        foreach ($expired as $profile) {
            $this->assertEquals($username, $profile->getUsername());
        }
        $this->profileRepository->removeProfiles($expired);

    }

    /**
     * @dataProvider provideProfiles
     * @throws \Exception
     */
    public function testActivate($exception, $token, $profile)
    {
        if ($exception !== ActivationException::class) {
            $profile = $this->profileService->prepareNewProfile($profile);
        }

        if ($exception) {
            $this->expectException($exception);
            $this->profileService->activateProfile($profile, $token);
        } else {
            $profile = $this->profileService->activateProfile($profile, $token);

            $this->assertTrue($profile->hasRole(Role::VERIFIED));
            $this->assertTrue($profile->hasRole(Role::USER));
            $this->assertFalse($profile->hasRole(Role::INACTIVE));
        }
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function provideProfiles()
    {
        $tokens = [];

        $token = new Token();
        $profile = new Profile();
        $token->setCreatedAt(new \DateTime());
        $expiresAt = new \DateTime();
        $token->setExpiresAt($expiresAt->add(new \DateInterval("PT1H")));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(true);
        $token->setProfile($profile);

        $tokens['invalid user'] = array(InvalidTokenException::class, $token, $profile);

        $token = new Token();
        $profile = new Profile();
        $token->setCreatedAt(new \DateTime('2018-01-01 00:00:00'));
        $token->setExpiresAt(new \DateTime('2012-01-01 00:00:00'));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(false);
        $token->setProfile($profile);

        $tokens['expired user'] = array(ExpiredTokenException::class, $token, $profile);

        $token = new Token();
        $profile = new Profile();
        $token->setCreatedAt(new \DateTime());
        $expiresAt = new \DateTime();
        $token->setExpiresAt($expiresAt->add(new \DateInterval("PT1H")));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(false);
        $token->setProfile($profile);

        $tokens['valid user'] = array(null, $token, $profile);

        $token = new Token();
        $profile = new Profile();
        $token->setCreatedAt(new \DateTime());
        $expiresAt = new \DateTime();
        $token->setExpiresAt($expiresAt->add(new \DateInterval("PT1H")));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(false);
        $token->setProfile($profile);

        $tokens['failed user'] = array(ActivationException::class, $token, $profile);

        return $tokens;

    }
}
