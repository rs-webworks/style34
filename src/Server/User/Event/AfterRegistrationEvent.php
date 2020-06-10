<?php declare(strict_types=1);

namespace EryseClient\Server\User\Event;

use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class AfterRegistrationEvent
 */
class AfterRegistrationEvent extends Event
{
    /**
     * @var UserEntity
     */
    public UserEntity $userEntity;

    /**
     * AfterRegistrationEvent constructor.
     *
     * @param UserEntity $userEntity
     */
    public function __construct(UserEntity $userEntity)
    {
        $this->userEntity = $userEntity;
    }

    /**
     * @return UserEntity
     */
    public function getUserEntity() : UserEntity
    {
        return $this->userEntity;
    }

}
