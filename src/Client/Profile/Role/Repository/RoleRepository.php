<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Role\Repository;

use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Client\Exception\ResourceNotFoundException;
use EryseClient\Client\Profile\Role\Entity\RoleEntity;
use EryseClient\Common\Repository\AbstractRepository;

/**
 * Class RoleRepository
 *
 *
 * @method RoleEntity[]|null findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method RoleEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoleEntity|null find($id, $lockMode = null, $lockVersion = null)
 */
class RoleRepository extends AbstractRepository
{

    /**
     * RoleRepository constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleEntity::class);
    }

    /**
     * @param string $name
     *
     * @return RoleEntity|null
     */
    public function findOneByName(string $name) : ?RoleEntity
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @param string $name
     *
     * @return RoleEntity
     * @throws ResourceNotFoundException
     */
    public function getOneByName(string $name) : RoleEntity
    {
        $result = $this->findOneByName($name);

        if ($result === null) {
            throw new ResourceNotFoundException('Missing Role: ' . $name);
        }

        return $result;
    }

    /**
     * @param array $listOfNames
     *
     * @return array
     */
    public function findByName(array $listOfNames) : array
    {
        return $this->findBy(['name' => $listOfNames]);
    }
}
