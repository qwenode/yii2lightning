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
    
    public static function setResponseFormatAsJson(): void
    {
        self::response()->format = \yii\web\Response::FORMAT_JSON;
    }

    public static function setResponseFormatAsJavascript(): void
    {
        self::response()->format = \yii\web\Response::FORMAT_RAW;
        self::response()->getHeaders()->set('Content-Type', 'application/javascript; charset=UTF-8');
    }

    public static function setResponseFormatAsCss(): void
    {
        self::response()->format = \yii\web\Response::FORMAT_RAW;
        self::response()->getHeaders()->set('Content-Type', 'text/css; charset=UTF-8');
    }
    /**
     * @param array|mixed $data
     * @param string $msg
     * @return array
     */
    public static function data($data, $msg = 'ok'): array
    {
        return [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
        ];
    }
    
    public static function success($msg = 'ok', $code = 1): array
    {
        return [
            'code' => $code,
            'msg'  => $msg,
        ];
    }
    
    public static function error($msg, $code = 0): array
    {
        return [
            'code' => $code,
            'msg'  => $msg,
        ];
    }
    
}