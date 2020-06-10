<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Facade;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Validator\ProfileValidator;

/**
 * Class EditProfileFacade
 */
class EditProfileFacade
{
    /**
     * @var ProfileRepository
     */
    private ProfileRepository $profileRepository;

    /**
     * EditProfileFacade constructor.
     *
     * @param ProfileRepository $profileRepository
     */
    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param ProfileValidator $validator
     * @param ProfileEntity $profileEntity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateProfileFromValidator(ProfileValidator $validator, ProfileEntity $profileEntity) : void
    {
        $profileEntity->setRole($validator->role);
        $this->profileRepository->save($profileEntity);
    }
}
