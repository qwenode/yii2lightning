<?php


namespace qwenode\yii2lightning\http;


use ErrorException;
use qwenode\yii2lightning\LightningHelper;

/**
 * yii request 快捷方式
 * Class Reqs
 * @package qwenode\yii2lightning\http
 */
class Reqs
{
    /**
     * @return bool
     */
    public static function isPOST()
    {
        return LightningHelper::getRequest()->getIsPost();
    }

    /**
     * @throws ErrorException
     */
    public static function isPOSTOrThrow()
    {
        if (!self::isPOST()) {
            throw new ErrorException('request method not allowed');
        }
    }

    /**
     * @throws ErrorException
     */
    public static function isGETOrThrow()
    {
        if (!self::isGET()) {
            throw new ErrorException('request method not allowed');
        }
    }

    /**
     * @return bool
     */
    public static function isGET()
    {
        return LightningHelper::getRequest()->getIsGet();
    }

    /**
     * @return bool
     */
    public static function isAJAX()
    {
        return LightningHelper::getRequest()->getIsAjax();
    }

    /**
     * @param null $name
     * @param null $default
     * @return array|mixed
     */
    public static function post($name = NULL, $defaultValue = NULL)
    {
        return LightningHelper::getRequest()->post($name, $defaultValue);
    }

    /**
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed
     */
    public static function get($name = NULL, $defaultValue = NULL)
    {
        return LightningHelper::getRequest()->get($name, $defaultValue);
    }

    /**
     * get trim(strip_tags($_POST[$name]));
     * @param $name
     * @return string
     */
    public static function postString($name)
    {
        return trim(strip_tags(self::post($name)));
    }

    /**
     * get trim(strip_tags($_GET[$name]));
     * @param $name
     * @return string
     */
    public static function getString($name)
    {
        return trim(strip_tags(self::get($name)));
    }

    /**
     * get (int)$_POST[$name]
     * @param $name
     * @return int
     */
    public static function postInteger($name)
    {
        return (int)self::post($name);
    }

    /**
     * get (int)$_GET[$name]
     * @param $name
     * @return int
     */
    public static function getInteger($name)
    {
        return (int)self::get($name);
    }

    /**
     * get (int)$_GET['id']
     * @return int
     */
    public static function getId()
    {
        return self::getInteger('id');
    }

    /**
     * get (int)$_POST['id']
     * @return int
     */
    public static function postId()
    {
        return self::postInteger('id');
    }
}