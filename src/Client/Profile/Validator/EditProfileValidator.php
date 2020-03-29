<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Validator;

use EryseClient\Client\Profile\Entity\ProfileEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EditProfileValidator
 */
class EditProfileValidator
{
    /**
     * @Assert\Type(type="EryseClient\Client\Profile\Role\Entity\RoleEntity")
     */
    public $role;

    /**
     * @param ProfileEntity $profileEntity
     *
     * @return EditProfileValidator
     */
    public static function fromProfile(ProfileEntity $profileEntity) : EditProfileValidator
    {
        $validator = new self();
        $validator->role = $profileEntity->getRole();

        return $validator;
    }

}
