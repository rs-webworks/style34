<?php declare(strict_types=1);

namespace EryseClient\Utility;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait SaveEntityTrait
 * @package EryseClient\Utility
 */
trait SaveEntityTrait
{

    /** @var EntityManagerInterface */
    protected $_em;

    public function save(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->_em->persist($entity);
        }

        $this->_em->flush();
    }

}