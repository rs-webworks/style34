<?php

namespace Style34\Tests\Service;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Style34\Service\TokenService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TokenServiceTest
 * @package Style34\Tests\Service
 * @covers \Style34\Service\TokenService
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
     * @covers \Style34\Service\TokenService::generateActivationToken
     */
    public function testGenerateActivationToken()
    {
        $allTokens = [];

        for ($i = 0; $i <= 10; $i++) {
            $generatedToken = $this->tokenService->generateActivationToken();
            $this->assertNotEmpty($generatedToken);
            $this->assertEquals(40, strlen($generatedToken));
            $this->assertFalse(array_key_exists($generatedToken, $allTokens));

            $allTokens[] = $generatedToken;
        }
    }

    /**
     * @dataProvider provideExpirationTokens
     * @throws \Exception
     * @covers \Style34\Service\TokenService::isExpired
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
     * @covers \Style34\Service\TokenService::isValid
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
     * @covers \Style34\Service\TokenService::createExpirationDateTime
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
