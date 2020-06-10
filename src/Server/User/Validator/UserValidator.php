<?php declare(strict_types=1);

namespace EryseClient\Server\User\Validator;

use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateUserValidator
 */
class UserValidator
{
    public const GROUP_REGISTRATION = 'registration';
    public const GROUP_RESET_PASSWORD = 'reset-password'; //TODO
    public const GROUP_EDIT = 'edit';

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(
     *     message="user.username-required",
     *     groups={UserValidator::GROUP_REGISTRATION}
     * )
     * @Assert\Length(min=3, max=20,
     *      minMessage="user.username-min-length",
     *      maxMessage="user.username-max-length",
     *      groups={UserValidator::GROUP_REGISTRATION}
     * )
     * @Assert\Regex(
     *      pattern="/^[A-Za-z0-9]+(?:[ _-][A-Za-z0-9]+)*$/",
     *      message="user.username-invalid",
     *      groups={UserValidator::GROUP_REGISTRATION}
     * )
     */
    public $username;

    /**
     * @Assert\Type("string")
     * @Assert\Email(
     *     message="user.email-invalid",
     *     groups={UserValidator::GROUP_REGISTRATION}
     * )
     * @Assert\NotBlank(
     *     message="user.email-required",
     *     groups={UserValidator::GROUP_REGISTRATION}
     * )
     */
    public $email;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(
     *     message="user.password-required",
     *     groups={UserValidator::GROUP_REGISTRATION}
     * )
     * @Assert\Length(min=6, max=4096,
     *     minMessage="user.password-min-length",
     *     maxMessage="user.password-max.length",
     *     groups={UserValidator::GROUP_REGISTRATION}
     * )
     */
    public $plainPassword;

    /**
     * @param UserEntity $userEntity
     *
     * @return UserValidator
     */
    public static function fromUser(UserEntity $userEntity) : UserValidator
    {
        $validator = new self();
        $validator->username = $userEntity->getUsername();
        $validator->email = $userEntity->getEmail();
        $validator->plainPassword = $userEntity->getPlainPassword();

        return $validator;
    }
}
