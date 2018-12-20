<?php

namespace Style34\Service;

use OpenCrypt\OpenCrypt;

/**
 * Class CryptService
 * @package Style34\Service
 */
final class CryptService extends AbstractService
{

    /** @var string */
    private $appSecret;

    /** @var int */
    private $iv;

    /** @var OpenCrypt $openCrypt */
    private $openCrypt;

    /**
     * CryptService constructor.
     * @param string $appSecret
     * @param string $iv
     */
    public function __construct(string $appSecret, string $iv)
    {
        $this->appSecret = $appSecret;
        $this->iv = $iv;
        $this->openCrypt = new OpenCrypt($appSecret, $iv);
    }

    /**
     * @param string $val
     * @return string
     */
    public function encrypt(string $val): string
    {
        return $this->openCrypt->encrypt($val);
    }

    /**
     * @param string $val
     * @return string
     */
    public function decrypt(string $val): string
    {
        return $this->openCrypt->decrypt($val);
    }
}