<?php declare(strict_types=1);

namespace EryseClient\Entity\Client\Profile;

use Doctrine\ORM\Mapping as ORM;
use EryseClient\Entity\Common\Identifier;
use EryseClient\Entity\Server\User\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Profile
 * @package EryseClient\Entity\Client\Profile
 * @ORM\Entity(repositoryClass="EryseClient\Repository\Client\Profile\ProfileRepository")
 */
class Profile
{

    use Identifier;

    /**
     * @var User
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull(message="profile.state-required")
     */
    protected $state;

    /**
     * @ORM\Column(nullable=true, type="string")
     */
    protected $city;

    /**
     * @var \DateTime $birthdate
     * @ORM\Column(nullable=true, type="datetime")
     */
    protected $birthdate;


    // Methods
    // -----------------------------------------------------------------------------------------------------------------


    // Birthdate
    // -----------------------------------------------------------------------------------------------------------------

    public function getBirthdate(): \DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTime $birthdate): void
    {
        $this->birthdate = $birthdate;
    }





    // State
    // -----------------------------------------------------------------------------------------------------------------

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }


    // City
    // -----------------------------------------------------------------------------------------------------------------

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city): void
    {
        $this->city = $city;
    }


}