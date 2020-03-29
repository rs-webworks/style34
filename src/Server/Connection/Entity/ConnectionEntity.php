<?php declare(strict_types=1);

namespace EryseClient\Server\Connection\Entity;

use EryseClient\Common\Entity\Identifier;
use EryseClient\Common\Entity\ServerEntity;

/**
 * Class ConnectionEntity
 */
class ConnectionEntity implements ServerEntity
{

    use Identifier;

    private int $clientId;

    private int $profileId;

    private int $userId;
}
