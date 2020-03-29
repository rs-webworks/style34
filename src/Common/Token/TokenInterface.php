<?php declare(strict_types=1);


namespace EryseClient\Common\Token;

/**
 * Interface Token
 *
 *
 */
interface TokenInterface
{
    public const EXPIRY_MINUTE = 60;
    public const EXPIRY_HOUR = self::EXPIRY_MINUTE * 60;
    public const EXPIRY_DAY = self::EXPIRY_HOUR * 24;
    public const EXPIRY_WEEK = self::EXPIRY_DAY * 7;
    public const EXPIRY_MONTH = self::EXPIRY_DAY * 30;

    /**
     * @return string
     */
    public function getHash(): string;

    /**
     * @return TokenTypeInterface
     */
    public function getType(): TokenTypeInterface;

    /**
     * @return bool
     */
    public function isValid(): bool;

}
