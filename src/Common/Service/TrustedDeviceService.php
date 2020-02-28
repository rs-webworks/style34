<?php declare(strict_types=1);

namespace EryseClient\Common\Service;

use BrowscapPHP\Browscap;
use EryseClient\Common\Utility\LoggerAwareTrait;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManagerInterface;

/**
 * Class TrustedDeviceService
 *
 *
 */
final class TrustedDeviceService extends AbstractService implements TrustedDeviceManagerInterface
{
    use LoggerAwareTrait;

    /** @var Browscap */
    private $browscap;

    /**
     * TrustedDeviceService constructor.
     *
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
        // TODO: add functionality for trusted devices
    }

    /**
     * @param mixed $user
     * @param string $firewallName
     *
     * @return bool
     */
    public function isTrustedDevice($user, string $firewallName): bool
    {
        return false;
    }
}
