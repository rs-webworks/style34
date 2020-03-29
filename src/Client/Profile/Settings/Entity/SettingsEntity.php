<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Settings\Entity;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Common\Entity\ClientEntity;
use EryseClient\Common\Entity\Identifier;

/**
 * Class Settings
 * @ORM\Table(name="profile_settings")
 * @ORM\Entity(repositoryClass="EryseClient\Client\Profile\Settings\Repository\SettingsRepository")
 */
class SettingsEntity implements ClientEntity
{
    use Identifier;

    /**
     * TODO: Join both profile & user settings to be connected entity, not via id
     *
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $profileId;

    /**
     * SettingsEntity constructor.
     *
     * @param ProfileEntity|null $profile
     */
    public function __construct(ProfileEntity $profile = null)
    {
        $this->setProfileId($profile->getId());
    }

    /**
     * @return string
     */
    public function getProfileId() : string
    {
        return $this->profileId;
    }

    /**
     * @param string $profileId
     */
    public function setProfileId(string $profileId) : void
    {
        $this->profileId = $profileId;
    }
}
