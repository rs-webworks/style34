<?php

namespace eRyseClient\Traits;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait SaveEntityTrait
 * @package eRyseClient\Traits
 */
trait SaveEntityTrait
{

    /** @var EntityManagerInterface */
    protected $_em;

    /**
     * @param array $entities
     */
    public function save(...$entities)
    {
        foreach ($entities as $entity) {
            $this->_em->persist($entity);
        }

        $this->_em->flush();
    }
}