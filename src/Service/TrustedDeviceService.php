<?php declare(strict_types=1);

namespace EryseClient\Service;

use BrowscapPHP\Browscap;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManagerInterface;
use EryseClient\Utility\LoggerTrait;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class TrustedDeviceService
 * @package EryseClient\Service
 */
final class TrustedDeviceService extends AbstractService implements TrustedDeviceManagerInterface
{
    use LoggerTrait;

    /** @var Browscap */
    private $browscap;

    public function __construct(Browscap $browscap)
    {
        $this->browscap = $browscap;
    }

    public function addTrustedDevice($user, string $firewallName): void
    {
        // TODO: add functionality for trusted devices
    }

    public function isTrustedDevice($user, string $firewallName): bool
    {
        return false;
    }

}