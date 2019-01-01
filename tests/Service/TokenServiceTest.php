<?php

namespace EryseClient\Tests\Service;

use EryseClient\Entity\Profile\Profile;
use EryseClient\Entity\Token\Token;
use EryseClient\Entity\Token\TokenType;
use EryseClient\Service\TokenService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TokenServiceTest
 * @package EryseClient\Tests\Service
 * @covers \EryseClient\Service\TokenService
 */
class TokenServiceTest extends WebTestCase
{
    /** @var TokenService $tokenService */
    protected $tokenService;

    /**
     *
     */
    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->tokenService = $container->get(TokenService::class);
    }

    /**
     * @throws \Exception
     * @covers \EryseClient\Service\TokenService::generateActivationToken
     */
    public function testGetActivationToken()
    {
        $tokenHashes = [];

        for ($i = 0; $i <= 10; $i++) {
            $token = $this->tokenService->getActivationToken(new Profile());

            $ca = $token->getCreatedAt();
            $ea = $token->getExpiresAt();

            $this->assertEquals('000200', $ca->diff($ea)->format('%y%m%a%h%i%s'));
            $this->assertEquals(TokenType::PROFILE['ACTIVATION'], $token->getType()->getName());
            $this->assertEquals(40, strlen($token->getHash()));
            $this->assertFalse(array_key_exists($token->getHash(), $tokenHashes));

            $tokenHashes[] = $token->getHash();
        }

    }

    /**
     * @dataProvider provideExpirationTokens
     * @throws \Exception
     * @covers \EryseClient\Service\TokenService::isExpired
     */
    public function testIsExpired($expired, $token)
    {
        if($expired){
            $this->assertTrue($this->tokenService->isExpired($token));
        }else{
            $this->assertFalse($this->tokenService->isExpired($token));
        }
    }
    /**
     * @covers \EryseClient\Service\TokenService::isValid
     */
    public function testIsValid(){
       $validToken = new Token();
       $validToken->setInvalid(false);

       $invalidToken = new Token();
       $invalidToken->setInvalid(true);

       $this->assertTrue($this->tokenService->isValid($validToken));
       $this->assertFalse($this->tokenService->isValid($invalidToken));
    }


    /**
     * @throws \Exception
     * @covers \EryseClient\Service\TokenService::createExpirationDateTime
     */
    public function testCreateExpiraitonDateTime(){
        $startTime = new \DateTime('2018-01-01 00:00:00');
        $expectedExpiryTime = new \DateTime('2018-01-02 00:00:00');

        $reultTime = $this->tokenService->createExpirationDateTime($startTime, Token::EXPIRY_DAY);

        $this->assertEquals($expectedExpiryTime, $reultTime);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function provideExpirationTokens()
    {
        $tokens = [];

        $token = new Token();
        $token->setCreatedAt(new \DateTime('2018-01-01 00:00:00'));
        $token->setExpiresAt(new \DateTime('2012-01-01 00:00:00'));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(false);
        $token->setProfile(new Profile());

        $tokens['expired token'] = array(true, $token);

        $token = new Token();
        $token->setCreatedAt(new \DateTime('2018-01-01 00:00:00'));
        $expires = new \DateTime();
        $token->setExpiresAt($expires->add(new \DateInterval("PT1H")));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(false);
        $token->setProfile(new Profile());

        $tokens['un-exired token'] = array(false, $token);

        return $tokens;

    }
}
