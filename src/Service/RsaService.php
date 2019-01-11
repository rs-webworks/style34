<?php

namespace EryseClient\Service;

use EryseClient\Exception\Security\InvalidKeyTypeException;
use EryseClient\Exception\Security\KeysAlreadyExistsException;
use phpseclib\Crypt\RSA;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class RsaService
 * @package EryseClient\Service
 */
final class RsaService extends AbstractService
{
    const PUBLIC_KEY_FILE = '/config/rsa/public.key';
    const PRIVATE_KEY_FILE = '/config/rsa/private.key';

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var RSA
     */
    private $rsa;

    /**
     * RsaService constructor.
     * @param KernelInterface $kernel
     * @param RSA $rsa
     */
    public function __construct(KernelInterface $kernel, RSA $rsa)
    {
        $this->kernel = $kernel;
        $this->rsa = $rsa;
    }

    /**
     * @param string $token
     * @param bool $overwrite
     * @return void
     * @throws KeysAlreadyExistsException
     */
    public function generateKeyPair(string $token, bool $overwrite = false): void
    {
        if ($overwrite === false) {
            if ($this->checkForKeys()) {
                throw new KeysAlreadyExistsException();
            }
        }

        $this->rsa->setPassword($token);

        /**
         * @var $publickey  string
         * @var $privatekey string
         */
        extract($this->rsa->createKey());

        file_put_contents($this->kernel->getProjectDir() . self::PUBLIC_KEY_FILE, $publickey);
        file_put_contents($this->kernel->getProjectDir() . self::PRIVATE_KEY_FILE, $privatekey);
    }

    /**
     * @return bool
     */
    public function checkForKeys(): bool
    {
        return file_exists($this->kernel->getProjectDir() . self::PUBLIC_KEY_FILE) ||
            file_exists($this->kernel->getProjectDir() . self::PRIVATE_KEY_FILE);
    }

    /**
     * Used by holder of Private key to sign a message
     * @param $message
     * @return string
     * @throws InvalidKeyTypeException
     */
    public function rsaSignMessage($message): string
    {
        $this->rsa->loadKey($this->loadPrivateKey());
        $message = serialize($message);

        return $this->rsa->sign($message);
    }

    /**
     * Used by holder of Public key to verify message against signature
     * @param $message
     * @param string $signature
     * @return bool
     * @throws InvalidKeyTypeException
     */
    public function rsaVerifyMessage($message, string $signature)
    {
        $this->rsa->loadKey($this->loadPublicKey());
        $message = serialize($message);

        return $this->rsa->verify($message, $signature);
    }

    /**
     * Used by holder of Public Key to encode message
     * @param string $message
     * @param string $keyType Use RsaService constant
     * @return string
     * @throws InvalidKeyTypeException
     */
    public function rsaEncodeMessage($message, string $keyType): string
    {
        $this->rsa->loadKey($this->loadKey($keyType));
        $message = serialize($message);

        return $this->rsa->encrypt($message);
    }

    /**
     * Used by owner of Private key to decode message
     * @param string $encodedData
     * @param string $keyType Use RsaService constant
     * @return string
     * @throws InvalidKeyTypeException
     */
    public function rsaDecodeMessage(string $encodedData, string $keyType)
    {
        $this->rsa->loadKey($this->loadKey($keyType));
        $decodedData = $this->rsa->decrypt($encodedData);

        return unserialize($decodedData);
    }

    /**
     * @return string
     * @throws InvalidKeyTypeException
     */
    public function loadPublicKey(): string
    {
        return $this->loadKey(self::PUBLIC_KEY_FILE);
    }

    /**
     * @return string
     * @throws InvalidKeyTypeException
     */
    private function loadPrivateKey(): string
    {
        return $this->loadKey(self::PRIVATE_KEY_FILE);
    }

    /**
     * @param string $key
     * @return string
     * @throws InvalidKeyTypeException
     */
    private function loadKey(string $key): string
    {
        if (in_array($key, [self::PUBLIC_KEY_FILE, self::PRIVATE_KEY_FILE])) {
            return file_get_contents($this->kernel->getProjectDir() . $key);
        }

        throw new InvalidKeyTypeException();
    }
}
