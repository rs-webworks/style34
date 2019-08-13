<?php declare(strict_types=1);

namespace EryseClient\Utility;

use EryseClient\Entity\Server\User\User;

/**
 * Trait FindByUserTrait
 * @package EryseClient\Utility
 */
trait FindByUserTrait
{

    public function findByUser(User $user)
    {
        return $this->findOneBy(["userId" => $user->getId()]);
    }

}