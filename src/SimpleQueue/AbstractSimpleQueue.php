<?php


namespace qwenode\yii2lightning\SimpleQueue;


use qwenode\yii2lightning\LightningHelper;
use qwenode\yii2lightning\SimpleQueue\encoder\JsonEncoder;

/**
 * Class AbstractSimpleQueue
 * @package qwenode\yii2lightning\SimpleQueue
 */
abstract class AbstractSimpleQueue
{
    /**
     * @var $encoder EncoderInterface
     */
    protected $encoder;

    /**
     * AbstractSimpleQueue constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @return static
     */
    public static function newInstance()
    {
        $abstractSimpleQueue = new static();
        $abstractSimpleQueue->withEncoder(new JsonEncoder());
        return $abstractSimpleQueue;
    }

    abstract public function enqueue($data): bool;

    abstract public function dequeue();

    abstract public function size(): int;

    /**
     * 设置编码器,将数据编码存入redis,或将取出的数据解码
     * @param EncoderInterface $encoder
     * @return AbstractSimpleQueue
     */
    public function withEncoder(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        return $this;
    }

    /**
     * 队列key
     * @return string
     */
    public function key(): string
    {
        return basename(static::class);
    }

    /**
     * 清空队列/删除队列
     * @return int
     */
    public function clear()
    {
        return LightningHelper::getKeepaliveRedis()->del(static::key());
    }

}