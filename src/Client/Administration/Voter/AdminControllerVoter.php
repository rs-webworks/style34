<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Voter;

use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Common\Voter\ControllerVoter;
use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AdminControllerVoter
 *
 *
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

        if (!$user instanceof UserEntity) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $user->getProfile());
        }
    }

    /**
     * @param UserEntity $user
     * @param ProfileEntity $profile
     *
     * @return bool
     */
    protected function canView(UserEntity $user, ProfileEntity $profile): bool
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
