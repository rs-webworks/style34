<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Dto;

use EryseClient\Client\Profile\Entity\Profile;

/**
 * Class ProfileView
 *
 * @package EryseClient\Client\Profile\Dto
 */
class ProfileDetail
{

    /** @var Profile */
    protected $profile;

    /** @var bool */
    protected $ownProfile;

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     *
     * @return ProfileDetail
     */
    public function setProfile(Profile $profile): ProfileDetail
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOwnProfile(): bool
    {
        return $this->ownProfile;
    }

    /**
     * @param bool $ownProfile
     *
     * @return ProfileDetail
     */
    public function setOwnProfile(bool $ownProfile): ProfileDetail
    {
        $this->ownProfile = $ownProfile;

        return $this;
    }

}
