<?php


namespace qwenode\yii2lightning\SimpleQueue;


interface EncoderInterface
{
    /**
     * 编码
     * @param $data
     * @return mixed
     */
    public function encode($data);

    /**
     * 解码
     * @param $data
     * @return mixed
     */
    public function decode($data);
}