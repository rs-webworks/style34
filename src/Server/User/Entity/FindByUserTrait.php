<?php declare(strict_types=1);

namespace EryseClient\Server\User\Entity;

/**
 * Trait FindByUserTrait
 * @package EryseClient\Utility
 */
trait FindByUserTrait
{

    /**
     * @param User $user
     * @return mixed
     */
    public function findByUser(User $user)
    {
        return $this->findOneBy(["userId" => $user->getId()]);
    }
}
