<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Type\Repository;

use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Common\Token\TokenTypeInterface;
use EryseClient\Server\Token\Type\Entity\TypeEntity;

/**
 * Class TokenTypeRepository
 *
 *
 * @method TypeEntity|TokenTypeInterface|null findOneBy(array $criteria, array $orderBy = null)
 */
class TypeRepository extends AbstractRepository
{
    /**
     * TokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEntity::class);
    }

    /**
     * @param string $type
     *
     * @return TypeEntity|null
     */
    public function findType(string $type): ?TypeEntity
    {
        return $this->findOneBy(['name' => $type]);
    }
}
