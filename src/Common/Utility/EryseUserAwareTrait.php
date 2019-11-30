<?php declare(strict_types=1);

namespace EryseClient\Common\Utility;

use EryseClient\Server\User\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Security;

/**
 * Trait UserAwareTrait
 *
 * @package EryseClient\Common\Utility
 */
trait EryseUserAwareTrait
{

    /** @var User $user */
    protected $user;

    /**
     * @required
     *
     * @param Security $security
     */
    public function setUser(Security $security)
    {
        $user = $security->getUser();

        if (!$user) {
            return;
        }

        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                "App relies on " . User::class . " but got " . get_class($user) . " instead."
            );
        }

        $this->user = $user;
    }
}
