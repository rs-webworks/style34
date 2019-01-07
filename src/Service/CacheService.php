<?php

namespace EryseClient\Service;


use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CacheService
 * @package EryseClient\Service
 */
final class CacheService extends AbstractService
{
    const EXPIRES_AFTER_MINUTE = 60;
    const EXPIRES_AFTER_HOUR = self::EXPIRES_AFTER_MINUTE * 60;
    const EXPIRES_AFTER_DAY = self::EXPIRES_AFTER_HOUR * 24;
    const EXPIRES_AFTER_WEEK = self::EXPIRES_AFTER_DAY * 7;
    const EXPIRES_AFTER_MONTH = self::EXPIRES_AFTER_DAY * 30;

    /**
     * @var CacheItemPoolInterface
     */
    private $pool;

    /**
     * CacheService constructor.
     * @param CacheItemPoolInterface $pool
     */
    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }


    /**
     * @param string $key
     * @param callable $callback
     * @param int $expiresAfter
     * @param array $arguments
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function callCached(string $key, callable $callback, int $expiresAfter = 0, array $arguments = [])
    {
        $item = $this->pool->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        $item->set($callback(...$arguments));
        $item->expiresAfter($expiresAfter);
        $this->pool->save($item);

        return $item->get();
    }

    /**
     *
     */
    public function clearCache()
    {
        $this->pool->clear();
    }

}