<?php declare(strict_types=1);

namespace EryseClient\Server\GoogleAuth\Entity;

use EryseClient\Common\Entity\ServerEntity;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Settings\Entity\SettingsEntity;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

/**
 * Class GoogleAuth
 *
 */
class GoogleAuthEntity implements TwoFactorInterface, ServerEntity
{
    /** @var UserEntity */
    protected $user;

    /** @var SettingsEntity */
    private $serverSettings;

    /**
     * GoogleAuth constructor.
     *
     * @param UserEntity $user
     * @param SettingsEntity $serverSettings
     */
    public function __construct(UserEntity $user, SettingsEntity $serverSettings)
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
