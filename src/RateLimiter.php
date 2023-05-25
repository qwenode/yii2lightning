<?php

namespace qwenode\yii2lightning;

use ErrorException;
use yii\caching\Cache;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;
use yii\redis\Connection;

/**
 *
 */
class RateLimitExceededException extends ErrorException
{
}

/**
 *
 */
class RateLimiter
{
    protected string $_collection;
    /**
     * @var CacheInterface|Connection|Cache
     */
    protected $_cache;
    protected string $_uniqueIdentifier;
    protected int $_limit;
    protected int $_lifetime;
    protected bool $_isRedis = false;

    public function __construct(string $collection, int $limit, int $minutes)
    {
        if ($minutes < 1) {
            $minutes = 1;
        }
        $this->_collection       = strtoupper(sprintf('LIGHTING_RATE_LIMITER:%s', sha1($collection)));
        $this->_uniqueIdentifier = $this->_collection;
        $this->_limit            = $limit;
        $this->_lifetime         = $minutes * 60;

        if (LightningHelper::getApplication()->has('redis')) {
            $this->_cache   = LightningHelper::getRedis();
            $this->_isRedis = true;
        } else {
            $this->_cache = LightningHelper::getCache();
        }
    }

    public static function withCollection(string $collection, int $limit = 5, int $minutes = 5): static
    {
        return new static($collection, $limit, $minutes);
    }

    /**
     * 最终的key=LIGHTING_RATE_LIMITER:collection:uniqueIdentifier+limit
     * @param string $uniqueIdentifier 根据唯一ID进行限制
     * @param int $limit 此参数也会加入ID计算规则,同样的 $uniqueIdentifier 不同的 $limit 则 限制计数不会冲突
     * @param int $minutes
     * @return RateLimiter
     */
    public function factor(string $uniqueIdentifier, int $limit, int $minutes = 5): RateLimiter
    {
        if ($minutes < 1) {
            $minutes = 1;
        }
        $rateLimiter                    = clone $this;
        $rateLimiter->_uniqueIdentifier = strtoupper(sprintf('%s:%s', $this->_collection, sha1($uniqueIdentifier . $limit)));
        $rateLimiter->_limit            = $limit;
        $rateLimiter->_lifetime         = $minutes * 60;
        return $rateLimiter;
    }

    public function hit(): void
    {
        $prev = (int)$this->_cache->get($this->_uniqueIdentifier)+1;
        if ($this->_isRedis) {
            $next = (int)$this->_cache->incr($this->_uniqueIdentifier);
            $this->_cache->expire($this->_uniqueIdentifier, $this->_lifetime);
        } else {
            $v = (int)$this->_cache->get($this->_uniqueIdentifier);
            $v++;
            $next = $v;
            $this->_cache->set($this->_uniqueIdentifier, $v, $this->_lifetime, new TagDependency(['tags' => $this->_collection]));
        }
        if ($prev != $next) {
            throw new RateLimitExceededException('Rate limit exceeded');
        }
    }

    public function invalidateFactor(): void
    {
        if ($this->_isRedis) {
            $this->_cache->del($this->_uniqueIdentifier);
        } else {
            $this->_cache->delete($this->_uniqueIdentifier);
        }
    }

    public function invalidateCollection(): void
    {
        if ($this->_isRedis) {
            $this->_cache->del(sprintf('%s:*', $this->_collection));
        } else {
            TagDependency::invalidate($this->_cache, $this->_collection);
        }
    }

    public function verify(): bool
    {
        $v = (int)$this->_cache->get($this->_uniqueIdentifier);
        return $v < $this->_limit;
    }

    /**
     * @return void
     * @throws RateLimitExceededException
     */
    public function verifyOrThrow(string $message = 'Rate limit exceeded'): void
    {
        if (!$this->verify()) {
            throw new RateLimitExceededException($message);
        }
    }
}