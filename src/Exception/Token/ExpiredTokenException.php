<?php declare(strict_types=1);

namespace EryseClient\Exception\Token;

use Throwable;

/**
 * Class InvalidTokenException
 * @package EryseClient\Exception\Profile
 */
class ExpiredTokenException extends TokenException implements Throwable
{
}
