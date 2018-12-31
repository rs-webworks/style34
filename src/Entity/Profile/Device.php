<?php

namespace eRyseClient\Entity\Profile;

use Doctrine\ORM\Mapping as ORM;
use eRyseClient\Entity\Identifier;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Device
 * @package eRyseClient\Entity\Profile
 * @ORM\Entity(repositoryClass="eRyseClient\Repository\Profile\DeviceRepository")
 * @UniqueEntity("name")
 */
class Device
{

    use Identifier;

    /**
     * @var string $name
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="profile.role.name-required")
     */
    protected $name;

    /**
     * @var string $cookieName
     * @ORM\Column(type="string", unique=true)
     */
    protected $cookieName;

    /**
     * @var Profile $profile
     * @ORM\ManyToOne(targetEntity="eRyseClient\Entity\Profile\Profile", inversedBy="devices")
     */
    protected $profile;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    /**
     * @param string $cookieName
     */
    public function setCookieName(string $cookieName): void
    {
        $this->cookieName = $cookieName;
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


}