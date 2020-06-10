<?php declare(strict_types=1);

namespace EryseClient\Server\User\Security\Validator;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ResetPasswordValidator
 */
class ResetPasswordValidator
{
    /**
     * @Assert\Type("string")
     */
    public $oldPassword;

    public $newPassword;
}
