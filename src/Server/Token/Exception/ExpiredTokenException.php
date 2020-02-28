<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Exception;

use Throwable;

/**
 * Class InvalidTokenException
 *
 */
class ExpiredTokenException extends TokenException implements Throwable
{
}
