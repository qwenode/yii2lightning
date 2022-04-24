<?php


namespace qwenode\yii2lightning\SimpleQueue\encoder;


use qwenode\yii2lightning\SimpleQueue\EncoderInterface;

/**
 * Class JsonEncoder
 * @package qwenode\yii2lightning\SimpleQueue\encoder
 */
class JsonEncoder implements EncoderInterface
{
    /**
     * json编码数据
     * @param $data
     * @return false|mixed|string
     */
    public function encode($data)
    {
        return \json_encode($data);
    }

    /**
     * json 解码
     * @param string $data
     * @return mixed
     */
    public function decode($data)
    {
        return \json_decode($data, TRUE);
    }
}