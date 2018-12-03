<?php

namespace Style34\Tests\Service;

use PHPUnit\Framework\TestCase;
use Style34\Service\TokenService;

class TokenServiceTest extends TestCase
{
    /** @var TokenService $tokenService */
    protected $tokenService;

    /**
     *
     */
    public function setUp()
    {
        $this->tokenService = new TokenService();
    }

    /**
     *
     */
    public function testGenerateActivationToken()
    {
    }
}
