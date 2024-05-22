<?php

namespace qwenode\yii2lightning;

use ErrorException;
use qwephp\SS;
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
    protected int $_limit;
    protected int $_lifetime;
    protected bool $_isRedis = false;
    protected int $shouldBe;

    protected function __construct(string $collection, int $limit, int $minutes)
    {
        if ($minutes < 1) {
            $minutes = 1;
        }
        $collection        = SS::removeSpecialChar($collection);
        $this->_collection = strtoupper(sprintf('LIGHTING_RATE_LIMITER:%s', $collection));
        $this->_limit      = $limit;
        $this->_lifetime   = $minutes * 60;

        if (LightningHelper::getApplication()->has('redis')) {
            $this->_cache   = LightningHelper::getRedis();
            $this->_isRedis = true;
        } else {
            $this->_cache = LightningHelper::getCache();
        }
        $this->shouldBe = (int)$this->_cache->get($this->_collection) + 1;
    }

    public static function withCollection(string $collection, int $limit = 5, int $minutes = 5): static
    {
        return new static($collection, $limit, $minutes);
    }

    /**
     * 最终的key=LIGHTING_RATE_LIMITER:parent-collection:factor-collection
     *
     * @param string $collection
     * @param int    $limit
     * @param int    $minutes
     *
     * @return RateLimiter
     */
    public function factor(string $collection, int $limit, int $minutes = 5): RateLimiter
    {
        if ($minutes < 1) {
            $minutes = 1;
        }
        $collection  = SS::removeSpecialChar($collection);
        $rateLimiter = clone $this;
        $rateLimiter->setCollection(strtoupper(sprintf('%s:%s', $this->_collection, $collection)));
        $rateLimiter->setLimit($limit);
        $rateLimiter->setLifeTime($minutes * 60);
        return $rateLimiter;
    }

    public function setCollection(string $value)
    {
        $this->_collection = $value;
    }

    public function setLimit(int $limit)
    {
        $this->_limit = $limit;
    }

    public function setLifeTime(int $lifeTime)
    {
        $this->_lifetime = $lifeTime;
    }

    public function hit(): void
    {
        if ($this->_isRedis) {
            $next = (int)$this->_cache->incr($this->_collection);
            $this->_cache->expire($this->_collection, $this->_lifetime);
        } else {
            $v = (int)$this->_cache->get($this->_collection);
            $v++;
            $next = $v;
            $this->_cache->set($this->_collection, $v, $this->_lifetime, new TagDependency(['tags' => $this->_collection]));
        }
        if ($this->shouldBe != $next) {
            throw new RateLimitExceededException('Rate limit exceeded');
        }
    }

    public function invalidateFactor(): void
    {
        if ($this->_isRedis) {
            $this->_cache->del($this->_collection);
        } else {
            $this->_cache->delete($this->_collection);
        }
    }

    public function invalidateCollection(): void
    {
        if ($this->_isRedis) {
            $this->_cache->del(sprintf('%s:*', $this->_collection));
            $this->_cache->del($this->_collection);
        } else {
            TagDependency::invalidate($this->_cache, $this->_collection);
        }
    }

    /**
     * @return void
     * @throws RateLimitExceededException
     */
    public function verifyOrThrow(string $message = 'Rate limit exceeded', int $code = 429): void
    {
        if (!$this->verify()) {
            throw new RateLimitExceededException($message,$code);
        }
    }

    public function verify(): bool
    {
        $v = (int)$this->_cache->get($this->_collection);
        return $v < $this->_limit;
    }
}