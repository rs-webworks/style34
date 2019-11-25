<?php declare(strict_types=1);

namespace EryseClient\Client\ProfileSecurity\Provider;

use EryseClient\Client\Profile\Entity\Profile;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Server\User\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package EryseClient\Security
 */
class ProfileProvider implements UserProviderInterface
{
    /** @var ProfileRepository */
    private $profileRepository;

    /**
     * UserProvider constructor.
     * @param ProfileRepository $profileRepository
     */
    public function __construct(
        ProfileRepository $profileRepository
    ) {
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param string $username
     * @return UserInterface
     * @throws Exception
     */
    public function loadUserByUsername($username)
    {
        $profile = $this->profileRepository->loadUserByUsername($username) ?? false;
        if (!$profile) {
            throw new UsernameNotFoundException();
        }

        return $profile;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     * @param UserInterface $profile
     * @return UserInterface
     */
    public function refreshUser(UserInterface $profile)
    {
        if (!$profile instanceof Profile) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($profile)));
        }

        $refreshedProfile = $this->profileRepository->find($profile->id);
        if ($refreshedProfile->getUser()
                ->getTrustedTokenVersion() !== $profile->getUser()
                ->getTrustedTokenVersion()) {
            throw new AuthenticationException();
        }

        return $refreshedProfile;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
