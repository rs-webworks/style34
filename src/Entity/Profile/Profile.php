<?php


namespace App\Entity\Profile;

use AppBundle\Entity\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Profile
 * @package App\Entity\Profile
 * @ORM\Entity
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class Profile {


	use Identifier;

	/**
	 * @var string $username
	 * @ORM\Column(type="string", unique=true)
	 * @Assert\NotBlank(message="Přezdívka je povinná")
	 * @Assert\Regex(
	 *     pattern="/^[A-Za-z0-9]+(?:[ _-][A-Za-z0-9]+)*$/",
	 *     message="Přezdívka smí obsahovat pouze znaky a-z, 0-9 a -_"
	 * )
	 */
	protected $username;


	/**
	 * @var string $email
	 * @ORM\Column(type="string", unique=true)
	 * @Assert\Email("Zadej email v platném formátu: nekdo@nekde.abc")
	 * @Assert\NotBlank(message="Email je povinný")
	 */
	protected $email;

	/**
	 * @var string $password
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Heslo nesmí být prázdné")
	 * @Assert\Length(min=6, max=20,
	 *     minMessage="Heslo musí mít minimálně {{ limit }} znaků",
	 *     maxMessage="Heslo musí mít maximálně {{ limit }} znaků"
	 * )
	 */
	protected $password;

	/**
	 * @var \DateTime $createdAt
	 * @ORM\Column(type="datetime")
	 */
	protected $createdAt;

	/**
	 * @var \DateTime $deletedAt
	 * @ORM\Column(type="datetime")
	 */
	protected $deletedAt;

	/**
	 * @var Role $role
	 * @ORM\ManyToOne(targetEntity="App\Entity\Profile\Role", inversedBy="profiles")
	 * @Assert\NotNull(message="Profil musí mít roli")
	 */
	protected $role;

	/**
	 * @return string
	 */
	public function getUsername(): string {
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername(string $username): void {
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email): void {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string {
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password): void {
		$this->password = $password;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime {
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt(\DateTime $createdAt): void {
		$this->createdAt = $createdAt;
	}

	/**
	 * @return \DateTime
	 */
	public function getDeletedAt(): \DateTime {
		return $this->deletedAt;
	}

	/**
	 * @param \DateTime $deletedAt
	 */
	public function setDeletedAt(\DateTime $deletedAt): void {
		$this->deletedAt = $deletedAt;
	}

	/**
	 * @return Role
	 */
	public function getRole(): Role {
		return $this->role;
	}

	/**
	 * @param Role $role
	 */
	public function setRole(Role $role): void {
		$this->role = $role;
	}


}