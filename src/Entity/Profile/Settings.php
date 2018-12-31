<?php

namespace eRyseClient\Entity\Profile;

use Doctrine\ORM\Mapping as ORM;
use eRyseClient\Entity\Identifier;

/**
 * Class Role
 * @package eRyseClient\Entity\Profile
 * @ORM\Entity(repositoryClass="eRyseClient\Repository\Profile\SettingsRepository")
 */
class Settings
{

    use Identifier;

    /**
     * @var Profile
     * @ORM\OneToOne(targetEntity="eRyseClient\Entity\Profile\Profile", inversedBy="settings")
     */
    protected $profile;

    /**
     * @var bool $name
     * @ORM\Column(type="boolean")
     */
    protected $twoStepAuthEnabled;

    /**
     * @var string $gAuthSecret
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gAuthSecret;


    /**
     * Settings constructor.
     * @param Profile|null $profile
     */
    public function __construct(Profile $profile = null)
    {
        $this->setProfile($profile);
        $this->twoStepAuthEnabled = false;
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile): void
    {
        $this->profile = $profile;
    }


    /**
     * @return bool
     */
    public function isTwoStepAuthEnabled(): bool
    {
        return $this->twoStepAuthEnabled;
    }

    /**
     * @param bool $twoStepAuthEnabled
     */
    public function setTwoStepAuthEnabled(bool $twoStepAuthEnabled): void
    {
        $this->twoStepAuthEnabled = $twoStepAuthEnabled;
    }

    /**
     * Always use CryptService to decode
     * @return string
     */
    public function getGAuthSecret(): string
    {
        return $this->gAuthSecret;
    }

    /**
     * Always use CryptService to encode
     * @param string $gAuthSecret
     */
    public function setGAuthSecret(?string $gAuthSecret): void
    {
        $this->gAuthSecret = $gAuthSecret;
    }


}