<?php declare(strict_types=1);

namespace Style34\Entity\State;

use Doctrine\ORM\Mapping as ORM;
use Style34\Entity\MasterData;
use Style34\Entity\Profile\Profile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class State
 * @package Style34\Entity\State
 * @ORM\Entity(repositoryClass="Style34\Repository\Address\StateRepository")
 * @UniqueEntity("name")
 */
class State {

    use MasterData;


    /**
     * @var Profile[]
     * @ORM\OneToMany(targetEntity="Style34\Entity\Profile\Profile", mappedBy="state")
     */
    protected $profiles;
}