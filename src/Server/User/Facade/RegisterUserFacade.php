<?php declare(strict_types=1);

namespace EryseClient\Server\User\Facade;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Factory\UserFactory;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Validator\UserValidator;

/**
 * Class CreateUserFacade
 */
class RegisterUserFacade
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var UserFactory
     */
    private UserFactory $userFactory;

    /**
     * RegisterUserFacade constructor.
     *
     * @param UserRepository $userRepository
     * @param UserFactory $userFactory
     */
    public function __construct(UserRepository $userRepository, UserFactory $userFactory)
    {
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
    }

    /**
     * @param UserValidator $validator
     * @param UserEntity $userEntity
     *
     * @param string|null $ip
     *
     * @return UserEntity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createFromValidator(UserValidator $validator, ?string $ip = null) : UserEntity
    {
        $userEntity = $this->userFactory->createNewUser(
            $validator->username,
            $validator->email,
            $validator->email,
            $ip
        );
        $this->userRepository->saveAndCreateSettings($userEntity);

        return $userEntity;
    }
}
