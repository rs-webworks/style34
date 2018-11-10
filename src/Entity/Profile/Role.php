<?php

namespace App\Entity\Profile;

use AppBundle\Entity\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Role
 * @package App\Entity\Profile
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class Role {

	use Identifier;

	/**
	 * @var string $name
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Název nesmí být prázdný", unique=true)
	 */
	protected $name;

	/**
	 * @var Profile[]
	 */
	protected $profiles;

	/**
	 * @var string $color
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Barva musí být vyplněna")
	 */
	protected $color;

}