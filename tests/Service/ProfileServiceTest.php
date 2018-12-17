<?php

namespace Style34\Tests\Service;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Style34\Exception\Profile\ActivationException;
use Style34\Exception\Token\ExpiredTokenException;
use Style34\Exception\Token\InvalidTokenException;
use Style34\Repository\Profile\ProfileRepository;
use Style34\Repository\Token\TokenTypeRepository;
use Style34\Service\ProfileService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileServiceTest
 * @package Style34\Tests\Service
 * @covers  \Style34\Service\ProfileService
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

        /** @var Profile[] $expired */
        $expired = $this->profileService->getExpiredRegistrations();
        foreach($expired as $profile){
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
