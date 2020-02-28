<?php declare(strict_types=1);

namespace EryseClient\Server\User\Entity;

/**
 * Trait FindByUserTrait
 *
 */
trait FindByUserTrait
{

    /**
     * @param UserEntity $user
     *
     * @return mixed
     */
    public function findByUser(UserEntity $user)
    {
        return $this->findOneBy(["userId" => $user->getId()]);
    }
}
