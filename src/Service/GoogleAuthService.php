<?php

namespace EryseClient\Service;


use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

/**
 * Class GoogleAuthService
 * @package EryseClient\Service
 */
final class GoogleAuthService extends AbstractService
{
    const CODE_LENGTH = 6;
    const SECRET_LENGTH = 10;

    /** @var GoogleAuthenticator $googleAuth */
    private $googleAuth;

    /**
     * GoogleAuthService constructor.
     * @param GoogleAuthenticator $googleAuth
     */
    public function __construct(GoogleAuthenticator $googleAuth)
    {
        $this->googleAuth = $googleAuth;
    }

    /**
     * @return string
     */
    public function generateSecret()
    {
        return $this->googleAuth->generateSecret();
    }

    /**
     * @param $email
     * @param $secret
     * @return string
     */
    public function generateQr($email, $secret)
    {
        return GoogleQrUrl::generate($email, $secret);
    }

    /**
     * @param $secret
     * @param $code
     * @return bool
     */
    public function checkCode($secret, $code)
    {
        return $this->googleAuth->checkCode($secret, $code);
    }
}