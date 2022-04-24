<?php


namespace qwenode\yii2lightning\SimpleQueue;


use qwenode\yii2lightning\LightningHelper;

/**
 * 随机出栈队列
 * Class AbstractRandomSimpleQueue
 * @package qwenode\yii2lightning\SimpleQueue
 */
abstract class AbstractRandomSimpleQueue extends AbstractSimpleQueue
{
    /**
     * @param $data
     * @return bool|int
     */
    public function enqueue($data): bool
    {
        if ($this->encoder instanceof EncoderInterface) {
            $data = $this->encoder->encode($data);
        }
        return LightningHelper::getKeepaliveRedis()->sadd(static::key(), $data);
    }

    /**
     * @return array|bool|mixed|string
     */
    public function dequeue()
    {
        $data = LightningHelper::getKeepaliveRedis()->spop(static::key());
        if ($data === FALSE) {
            return $data;
        }
        if ($this->encoder instanceof EncoderInterface) {
            $data = $this->encoder->decode($data);
        }
        return $data;
    }

    /**
     * 队列长度
     * @return int
     */
    public function size(): int
    {
        return LightningHelper::getKeepaliveRedis()->scard(static::key());
    }

}