<?php declare(strict_types=1);

namespace EryseClient\Tests\Client\Token\Service;

use DateInterval;
use DateTime;
use EryseClient\Model\Client\Token\Entity\Token;
use EryseClient\Model\Client\Token\Entity\TokenType;
use EryseClient\Model\Client\Token\Service\TokenService;
use EryseClient\Model\Server\User\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TokenServiceTest
 * @package EryseClient\Tests\Service
 * @covers \EryseClient\Model\Client\Token\Service\TokenService
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
     * @throws Exception
     * @covers \EryseClient\Model\Client\Token\Service\TokenService::generateActivationToken
     */
    public function testGetActivationToken()
    {
        $tokenHashes = [];

        for ($i = 0; $i <= 10; $i++) {
            $user = new User();
            $user->setId(1);
            $token = $this->tokenService->getActivationToken($user);

            $ca = $token->getCreatedAt();
            $ea = $token->getExpiresAt();

            $this->assertEquals(
                '000200',
                $ca->diff($ea)
                    ->format('%y%m%a%h%i%s')
            );
            $this->assertEquals(
                TokenType::USER['ACTIVATION'],
                $token->getType()
                    ->getName()
            );
            $this->assertEquals(40, strlen($token->getHash()));
            $this->assertFalse(array_key_exists($token->getHash(), $tokenHashes));

            $tokenHashes[] = $token->getHash();
        }
    }

    /**
     * @dataProvider provideExpirationTokens
     * @throws Exception
     * @covers       \EryseClient\Model\Client\Token\Service\TokenService::isExpired
     */
    public function testIsExpired($expired, $token)
    {
        if ($expired) {
            $this->assertTrue($this->tokenService->isExpired($token));
        } else {
            $this->assertFalse($this->tokenService->isExpired($token));
        }
    }

    /**
     * @covers \EryseClient\Model\Client\Token\Service\TokenService::isValid
     */
    public function testIsValid()
    {
        $validToken = new Token();
        $validToken->setInvalid(false);

        $invalidToken = new Token();
        $invalidToken->setInvalid(true);

        $this->assertTrue($this->tokenService->isValid($validToken));
        $this->assertFalse($this->tokenService->isValid($invalidToken));
    }

    /**
     * @throws Exception
     * @covers \EryseClient\Model\Client\Token\Service\TokenService::createExpirationDateTime
     */
    public function testCreateExpiraitonDateTime()
    {
        $startTime = new DateTime('2018-01-01 00:00:00');
        $expectedExpiryTime = new DateTime('2018-01-02 00:00:00');

        $reultTime = $this->tokenService->createExpirationDateTime($startTime, Token::EXPIRY_DAY);

        $this->assertEquals($expectedExpiryTime, $reultTime);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function provideExpirationTokens()
    {
        $tokens = [];

        $token = new Token();
        $token->setCreatedAt(new DateTime('2018-01-01 00:00:00'));
        $token->setExpiresAt(new DateTime('2012-01-01 00:00:00'));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(false);
        $token->setUserId(1);

        $tokens['expired token'] = [true, $token];

        $token = new Token();
        $token->setCreatedAt(new DateTime('2018-01-01 00:00:00'));
        $expires = new DateTime();
        $token->setExpiresAt($expires->add(new DateInterval("PT1H")));
        $token->setType(new TokenType());
        $token->setHash(sha1(random_bytes(4)));
        $token->setInvalid(false);
        $token->setUserId(1);

        $tokens['un-exired token'] = [false, $token];

        return $tokens;

    }
}
