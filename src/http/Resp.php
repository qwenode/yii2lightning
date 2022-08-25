<?php


namespace qwenode\yii2lightning\http;


use qwenode\yii2lightning\LightningHelper;
use yii\console\Response;

class Resp
{
    public static function response(): \yii\web\Response|Response
    {
        return LightningHelper::getResponse();
    }
    
    public static function setResponseFormatAsJson()
    {
        self::response()->format = \yii\web\Response::FORMAT_JSON;
    }
    
    /**
     * @param array|mixed $data
     * @param string $msg
     * @return array
     */
    public static function data($data, $msg = 'ok')
    {
        return [
            'code' => 1,
            'msg'  => $msg,
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