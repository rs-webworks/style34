<?php declare(strict_types=1);

namespace EryseClient\Common\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class AbstractRepository
 *
 */
abstract class AbstractRepository extends ServiceEntityRepository
{

    /**
     * @param mixed ...$entities
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(...$entities)
    {
        $em = $this->getEntityManager();

        foreach ($entities as $entity) {
            $em->persist($entity);
        }

        $em->flush();
    }
}
