<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Dto;

use EryseClient\Client\Profile\Entity\ProfileEntity;

/**
 * Class ProfileDetailDto
 */
class ProfileDetailDto
{

    /** @var ProfileEntity */
    public ProfileEntity $profile;

    /** @var bool */
    public bool $ownProfile;

}
