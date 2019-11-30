<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Voter;

use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Common\Voter\CrudVoter;
use EryseClient\Server\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AdminProfileVoter
 * @package EryseClient\Client\Administration\Profile\Voter
 */
class AdminProfileVoter extends CrudVoter
{
    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, self::ACTIONS)) {
            return false;
        }

        if ($subject !== null && !$subject instanceof Profile) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $userProfile = $user->getProfile();

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $userProfile);
        }
    }

    /**
     * @param Profile $subject
     * @param User $user
     * @param Profile $userProfile
     * @return bool
     */
    protected function canView(User $user, Profile $userProfile): bool
    {
        if ($this->userRoleService->isRoleAdmin($user)) {
            return true;
        }

        if ($this->profileRoleService->isRoleAdmin($userProfile)) {
            return true;
        }

        if ($this->profileRoleService->isRoleModerator($userProfile)) {
            return true;
        }

        return false;
    }
}
