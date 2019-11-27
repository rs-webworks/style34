<?php declare(strict_types=1);


namespace EryseClient\Common\Token;

/**
 * Interface Token
 *
 * @package EryseClient\Common\Token
 */
interface TokenInterface
{
    const EXPIRY_MINUTE = 60;
    const EXPIRY_HOUR = self::EXPIRY_MINUTE * 60;
    const EXPIRY_DAY = self::EXPIRY_HOUR * 24;
    const EXPIRY_WEEK = self::EXPIRY_DAY * 7;
    const EXPIRY_MONTH = self::EXPIRY_DAY * 30;

    /**
     * @return string
     */
    public function getHash(): string;

    /**
     * @return TokenTypeInterfance
     */
    public function getType(): TokenTypeInterfance;

    /**
     * @return bool
     */
    public function isValid(): bool;

}
