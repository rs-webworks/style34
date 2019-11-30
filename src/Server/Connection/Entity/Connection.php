<?php declare(strict_types=1);

namespace EryseClient\Server\Connection\Entity;

use EryseClient\Common\Entity\Identifier;
use EryseClient\Common\Entity\ServerEntity;
use Doctrine\ORM\Mapping\Annotation as ORM;

/**
 * Class Connection
 * @package EryseClient\Server\Connection\Entity
 */
class Connection implements ServerEntity
{

    use Identifier;

    private $clientId;

    private $profileId;

    private $userId;
}
