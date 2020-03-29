<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Voter;

use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Common\Voter\CrudVoter;
use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AdminProfileVoter
 *
 *
 */
class AdminProfileVoter extends CrudVoter
{
    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, self::ACTIONS)) {
            return false;
        }

        if ($subject !== null && !$subject instanceof ProfileEntity) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserEntity) {
            return false;
        }

        $userProfile = $user->getProfile();

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $userProfile);
            case self::EDIT:
                return $this->canEdit($user, $userProfile);
        }

        return false;
    }

    /**
     * @param UserEntity $user
     * @param ProfileEntity $userProfile
     *
     * @return bool
     */
    protected function canView(UserEntity $user, ProfileEntity $userProfile): bool
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

    /**
     * @param UserEntity $user
     * @param ProfileEntity $userProfile
     *
     * @return bool
     */
    protected function canEdit(UserEntity $user, ProfileEntity $userProfile): bool
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
