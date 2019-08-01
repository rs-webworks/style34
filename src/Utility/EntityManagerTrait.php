<?php declare(strict_types=1);
namespace EryseClient\Utility;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerTrait
 * @package EryseClient\Service\Utility
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