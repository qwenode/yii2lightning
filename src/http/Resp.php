<?php


namespace qwenode\yii2lightning\http;


class Resp
{
    /**
     * @param array|mixed $data
     * @return array
     */
    public static function data($data)
    {
        return [
            'code' => 1,
            'msg'  => 'ok',
            'data' => $data,
        ];
    }

    public static function success($msg = 'ok', $code = 1)
    {
        return [
            'code' => $code,
            'msg'  => $msg,
        ];
    }

    public static function error($msg, $code = 0)
    {
        return [
            'code' => $code,
            'msg'  => $msg,
        ];
    }

}