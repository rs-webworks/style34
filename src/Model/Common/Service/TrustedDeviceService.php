<?php declare(strict_types=1);

namespace EryseClient\Model\Common\Service;

use BrowscapPHP\Browscap;
use EryseClient\Component\Common\Utility\LoggerTrait;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManagerInterface;

/**
 * Class TrustedDeviceService
 * @package EryseClient\Service
 */
final class TrustedDeviceService extends AbstractService implements TrustedDeviceManagerInterface
{
    use LoggerTrait;

    /** @var Browscap */
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
        // TODO: add functionality for trusted devices
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
