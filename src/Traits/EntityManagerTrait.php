<?php

namespace EryseClient\Traits;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerTrait
 * @package EryseClient\Service\Traits
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