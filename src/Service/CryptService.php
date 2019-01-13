<?php

namespace EryseClient\Service;

use OpenCrypt\OpenCrypt;

/**
 * Class CryptService
 * @package EryseClient\Service
 */
final class CryptService extends AbstractService
{
    // TODO: Generate new secret & IV for each app in .env of somewhere
    const CRYPT_SECRET = 'e64e0ce4f66c48d3b95357d6f7cdc7b5';
    const CRYPT_IV = '1*]r[oě=,s¶#6@';

    /** @var OpenCrypt $openCrypt */
    private $openCrypt;

    /**
     * CryptService constructor.
     * @param string $appSecret
     * @param string $iv
     */
    public function __construct()
    {
        $this->openCrypt = new OpenCrypt(self::CRYPT_SECRET, self::CRYPT_IV);
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