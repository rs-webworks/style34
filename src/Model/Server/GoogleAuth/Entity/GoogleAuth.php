<?php declare(strict_types=1);

namespace EryseClient\Model\Server\GoogleAuth\Entity;

use EryseClient\Model\Server\User\Entity\User;
use EryseClient\Model\Server\UserSettings\Entity\UserSettings;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

/**
 * Class GoogleAuth
 * @package EryseClient\Entity\Server\User
 */
class GoogleAuth implements TwoFactorInterface
{
    /** @var User */
    protected $user;

    /** @var UserSettings */
    private $serverSettings;

    /**
     * GoogleAuth constructor.
     * @param User $user
     * @param UserSettings $serverSettings
     */
    public function __construct(User $user, UserSettings $serverSettings)
    {
        $this->user = $user;
        $this->serverSettings = $serverSettings;
    }

    /**
     * @return bool
     */
    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->serverSettings->isTwoStepAuthEnabled();
    }

    /**
     * @return string
     */
    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->user->getUsername();
    }

    /**
     * @return string
     */
    public function getGoogleAuthenticatorSecret(): string
    {
        return $this->serverSettings->getGAuthSecret();
    }
}
