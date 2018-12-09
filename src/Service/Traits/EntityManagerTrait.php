<?php

namespace Style34\Service\Traits;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerTrait
 * @package Style34\Service\Traits
 */
trait EntityManagerTrait
{

    /** @var EntityManagerInterface $em */
    protected $em;

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
}