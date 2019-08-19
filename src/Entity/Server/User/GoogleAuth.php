<?php declare(strict_types=1);

namespace EryseClient\Entity\Server\User;

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

/**
 * Class GoogleAuth
 * @package EryseClient\Entity\Server\User
 */
class GoogleAuth implements TwoFactorInterface
{
    /** @var User */
    protected $user;

    /** @var ServerSettings */
    private $serverSettings;

    /**
     * GoogleAuth constructor.
     * @param User $user
     * @param ServerSettings $serverSettings
     */
    public function __construct(User $user, ServerSettings $serverSettings)
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
