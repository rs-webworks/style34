<?php

namespace eRyseClient\Service;

use OpenCrypt\OpenCrypt;

/**
 * Class CryptService
 * @package eRyseClient\Service
 */
final class CryptService extends AbstractService
{
    // TODO: Generate new secret & IV for each app in .env of somewhere
    const CRYPT_SECRET = 'e64e0ce4f66c48d3b95357d6f7cdc7b5';
    const CRYPT_IV = '1*]r[oě=,s¶#6@';

    /** @var CryptService $_instance */
    private static $_instance;

    /** @var OpenCrypt $openCrypt */
    private $openCrypt;

    /**
     * CryptService constructor.
     * @param string $appSecret
     * @param string $iv
     */
    public function __construct()
    {
        if (!self::$_instance) {
            self::$_instance = $this->openCrypt = new OpenCrypt(self::CRYPT_SECRET, self::CRYPT_IV);
        } else {
            $this->openCrypt = self::$_instance;
        }
    }

    /**
     * @param string $val
     * @return string
     */
    public static function getEncrypted(string $val): string
    {
        if(!self::$_instance){
            self::$_instance = new OpenCrypt(self::CRYPT_SECRET, self::CRYPT_IV);
        }

        return self::$_instance->encrypt($val);
    }

    /**
     * @param string $val
     * @return string
     */
    public static function getDecrypted(string $val): string
    {
        if(!self::$_instance){
            self::$_instance = new OpenCrypt(self::CRYPT_SECRET, self::CRYPT_IV);
        }

        return self::$_instance->decrypt($val);
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