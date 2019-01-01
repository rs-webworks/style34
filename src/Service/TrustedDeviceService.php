<?php

namespace EryseClient\Service;

use BrowscapPHP\Browscap;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManagerInterface;
use EryseClient\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class TrustedDeviceService
 * @package EryseClient\Service
 */
final class TrustedDeviceService extends AbstractService implements TrustedDeviceManagerInterface
{
    use LoggerTrait;

    /**
     * @var Browscap
     */
    private $browscap;

    /**
     * TrustedDeviceService constructor.
     * @param Browscap $browscap
     */
    public function __construct(Browscap $browscap)
    {
        $this->browscap = $browscap;
    }


    /**
     * @param mixed $user
     * @param string $firewallName
     */
    public function addTrustedDevice($user, string $firewallName): void
    {

    }

    /**
     * @param mixed $user
     * @param string $firewallName
     * @return bool
     */
    public function isTrustedDevice($user, string $firewallName): bool
    {
        return false;
    }

}