<?php declare(strict_types=1);


namespace EryseClient\Entity\Server\User;


use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

class GoogleAuth implements TwoFactorInterface
{
    /** @var User */
    protected $user;

    /** @var ServerSettings  */
    private $serverSettings;

    public function __construct(User $user, ServerSettings $serverSettings)
    {
        $this->user = $user;
        $this->serverSettings = $serverSettings;
    }


    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->serverSettings->isTwoStepAuthEnabled();
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->user->getUsername();
    }

    public function getGoogleAuthenticatorSecret(): string
    {
        return $this->serverSettings->getGAuthSecret();
    }


}