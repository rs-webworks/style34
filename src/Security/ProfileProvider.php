<?php

namespace Style34\Security;

use Doctrine\ORM\EntityManagerInterface;
use Style34\Entity\Profile\Profile;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ProfileProvider implements UserProviderInterface
{
    /** @var EntityManagerInterface $em */
    protected $em;

    /**
     * ProfileProvider constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $username
     * @return UserInterface
     * @throws \Exception
     */
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository(Profile::class)->loadUserByUsername($username) ?? false;
        if(!$user){
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Profile) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->em->getRepository(Profile::class)->find($user->getId());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return Profile::class === $class;
    }
}
