<?php declare(strict_types=1);

namespace EryseClient\Server\User\Entity;

use EryseClient\Server\User\Exception\UserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Trait FindByUserTrait
 *
 */
trait FindByUserTrait
{

    /**
     * @param UserInterface $user
     *
     * @return mixed
     * @throws UserException
     */
    public function findByUser(UserInterface $user)
    {
        if (!($user instanceof UserEntity)) {
            throw new UserException('Finding by user is allowed only for UserEntity.');
        }

        return $this->findOneBy(['userId' => $user->getId()]);
    }
}
