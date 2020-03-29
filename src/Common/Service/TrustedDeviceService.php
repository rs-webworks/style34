<?php declare(strict_types=1);

namespace EryseClient\Common\Service;

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
