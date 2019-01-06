<?php

namespace EryseClient\Service;


use EryseClient\Exception\Application\KeysAlreadyExists;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ApplicationService
 * @package EryseClient\Service
 */
final class ApplicationService extends AbstractService
{
    const PUBLIC_KEY_FILE = '/config/rsa/public.key';
    const PRIVATE_KEY_FILE = '/config/rsa/private.key';
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * ApplicationService constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $token
     * @param bool $overwrite
     * @return void
     * @throws KeysAlreadyExists
     */
    public function generateKeyPair(string $token, bool $overwrite = false): void
    {
        if ($overwrite === false) {
            if (file_exists($this->kernel->getProjectDir() . self::PUBLIC_KEY_FILE) ||
                file_exists($this->kernel->getProjectDir() . self::PRIVATE_KEY_FILE))
            {
                throw new KeysAlreadyExists();
            }
        }

        $privateKey = $this->generatePrivateKey($token);
        $this->generatePublicKey($privateKey);
    }

    /**
     * @param string $token
     * @return resource
     */
    protected function generatePrivateKey(string $token)
    {
        $newKey = openssl_pkey_new(array(
            'private_key_bits' => 1024,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ));

        openssl_pkey_export_to_file($newKey, $this->kernel->getProjectDir() . self::PRIVATE_KEY_FILE, $token);

        return $newKey;
    }

    /**
     * @param resource $privateKey
     */
    protected function generatePublicKey($privateKey): void
    {
        $newKey = openssl_pkey_get_details($privateKey);
        file_put_contents($this->kernel->getProjectDir() . self::PUBLIC_KEY_FILE, $newKey['key']);

        openssl_free_key($privateKey);
    }

}