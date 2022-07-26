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
    protected $message = 'Rate limit exceeded';
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

    public function __construct(string $collection, int $limit, int $lifetime)
    {
        $this->_collection       = strtoupper(sprintf('LIGHTING_RATE_LIMITER:%s', $collection));
        $this->_uniqueIdentifier = $this->_collection;
        $this->_limit            = $limit;
        $this->_lifetime         = $lifetime;

        if (LightningHelper::getApplication()->has('redis')) {
            $this->_cache   = LightningHelper::getRedis();
            $this->_isRedis = true;
        } else {
            $this->_cache = LightningHelper::getCache();
        }
    }

    public static function withCollection(string $collection, int $limit = 5, int $lifetime = 300): static
    {
        return new static($collection, $limit, $lifetime);
    }

    public function factor(string $uniqueIdentifier, int $limit, int $lifetime = 300): RateLimiter
    {
        $rateLimiter                    = clone $this;
        $rateLimiter->_uniqueIdentifier = strtoupper(sprintf('%s:%s', $this->_collection, $uniqueIdentifier));
        $rateLimiter->_limit            = $limit;
        $rateLimiter->_lifetime         = $lifetime;
        return $rateLimiter;
    }

    public function hit(): void
    {
        if ($this->_isRedis) {
            $this->_cache->incr($this->_uniqueIdentifier);
            $this->_cache->expire($this->_uniqueIdentifier, $this->_lifetime);
        } else {
            $v = (int)$this->_cache->get($this->_uniqueIdentifier);
            $v++;
            $this->_cache->set($this->_uniqueIdentifier, $v, $this->_lifetime, new TagDependency(['tags' => $this->_collection]));
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
    public function verifyOrThrow(): void
    {
        if (!$this->verify()) {
            throw new RateLimitExceededException();
        }
    }
}