<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Validator;

use EryseClient\Client\Profile\Entity\ProfileEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EditProfileValidator
 */
class ProfileValidator
{
    /**
     * @Assert\Type(type="EryseClient\Client\Profile\Role\Entity\RoleEntity")
     */
    public $role;

    /**
     * @param ProfileEntity $profileEntity
     *
     * @return ProfileValidator
     */
    public static function fromProfile(ProfileEntity $profileEntity) : ProfileValidator
    {
        $validator = new self();
        $validator->role = $profileEntity->getRole();

        return $validator;
    }

}
