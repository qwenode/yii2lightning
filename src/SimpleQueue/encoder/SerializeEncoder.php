<?php

namespace qwenode\yii2lightning\SimpleQueue\encoder;

use qwenode\yii2lightning\SimpleQueue\EncoderInterface;

/**
 * 使用php serialize/unserialize 压缩/解压数据
 */
class SerializeEncoder implements EncoderInterface
{

    public function encode($data)
    {
        return serialize($data);
    }

    public function decode($data)
    {
        return unserialize($data);
    }
}