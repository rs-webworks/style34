<?php declare(strict_types=1);

namespace EryseClient\Common\Token;

/**
 * Interface TokenType
 *
 * @package EryseClient\Common\Token
 */
interface TokenTypeInterfance
{
    /**
     * @return string
     */
    public function getName(): string;
}