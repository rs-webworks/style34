<?php

namespace Style34\Tests\Service;

use Style34\Service\TokenService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;

        $this->tokenService = $container->get(TokenService::class);
        // ...
    }

    /**
     * @throws \Exception
     */
    public function testGenerateActivationToken()
    {

        $this->assertNotEmpty($this->tokenService->generateActivationToken());
    }
}
