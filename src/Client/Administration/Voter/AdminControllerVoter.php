<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Voter;

use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Common\Voter\ControllerVoter;
use EryseClient\Server\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AdminControllerVoter
 *
 * @package EryseClient\Client\Administration\Voter
 */
abstract class AdminControllerVoter extends ControllerVoter
{
    const TARGETS = [];

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool|void
     */
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, self::ACTIONS)) {
            return false;
        }

        if (!in_array($subject, static::TARGETS)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool|void
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $user->getProfile());
        }
    }

    /**
     * @param User $user
     * @param Profile $profile
     *
     * @return bool
     */
    protected function canView(User $user, Profile $profile): bool
    {
        if ($this->userRoleService->isRoleAdmin($user)) {
            return true;
        }

        if ($this->profileRoleService->isRoleAdmin($profile) || $this->profileRoleService->isRoleModerator($profile)) {
            return true;
        }

        return false;
    }
}
