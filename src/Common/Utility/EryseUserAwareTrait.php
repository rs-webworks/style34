<?php declare(strict_types=1);

namespace EryseClient\Common\Utility;

use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Security;

/**
 * Trait UserAwareTrait
 *
 *
 */
trait EryseUserAwareTrait
{

    /** @var UserEntity $user */
    protected UserEntity $user;

    /**
     * @required
     *
     * @param Security $security
     */
    public function setUser(Security $security) : void
    {
        $user = $security->getUser();

        if (!$user) {
            return;
        }

        if (!$user instanceof UserEntity) {
            throw new UnsupportedUserException(
                'App relies on ' . UserEntity::class . ' but got ' . get_class($user) . ' instead.'
            );
        }

        $this->user = $user;
    }
}
