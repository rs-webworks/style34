<?php declare(strict_types=1);

namespace EryseClient\Model\Client\Token\Exception;

use Throwable;

/**
 * Class InvalidTokenException
 * @package EryseClient\Exception\Profile
 */
class ExpiredTokenException extends TokenException implements Throwable
{
}
