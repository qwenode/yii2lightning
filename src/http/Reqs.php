<?php


namespace qwenode\yii2lightning\http;


use ErrorException;
use qwenode\yii2lightning\LightningHelper;
use qwephp\NN;
use qwephp\SS;
use yii\web\Request;

/**
 * yii request 快捷方式
 * Class Reqs
 * @package qwenode\yii2lightning\http
 */
class Reqs
{
    public static function getJsonBodyAsArray()
    {
        $rawBody = static::request()->getRawBody();
        return (array)json_decode($rawBody, true);
    }
    
    public static function getJsonID()
    {
        $jsonBodyAsArray = self::getJsonBodyAsArray();
        return key_exists('id',$jsonBodyAsArray) ?intval($jsonBodyAsArray['id']): 0;
    }
    /**
     * @return \yii\console\Request|Request
     */
    public static function request()
    {
        return LightningHelper::getRequest();
    }

    /**
     * @throws ErrorException
     */
    public static function isPOSTOrThrow()
    {
        if (!self::isPOST()) {
            throw new ErrorException('request method not allowed', 405);
        }
    }

    /**
     * @return bool
     */
    public static function isPOST(): bool
    {
        return static::request()->getIsPost();
    }

    /**
     * @throws ErrorException
     */
    public static function isGETOrThrow()
    {
        if (!self::isGET()) {
            throw new ErrorException('request method not allowed', 405);
        }
    }

    /**
     * @return bool
     */
    public static function isGET(): bool
    {
        return static::request()->getIsGet();
    }

    /**
     * @return bool
     */
    public static function isAJAX(): bool
    {
        return static::request()->getIsAjax();
    }

    /**
     * check the name whether is exists in $_GET array
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasGet(string $name): bool
    {
        return isset($_GET[$name]);
    }

    /**
     * check the name whether is exists in $_POST array
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasPost(string $name): bool
    {
        return isset($_POST[$name]);
    }

    /**
     * check the name whether is exists in $_FILES array
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasFile(string $name): bool
    {
        return isset($_FILES[$name]);
    }

    /**
     * get trim(strip_tags($_POST[$name]));
     *
     * @param string $name
     * @param bool   $strip trim(strip_tags(
     *
     * @return string
     */
    public static function postString(string $name, bool $strip = true)
    {
        $val = self::post($name);
        if ($val == null) {
            return '';
        }
        if (true === $strip) {
            $val = trim(strip_tags($val));
        }
        return $val;
    }

    /**
     * @param null $name
     * @param null $default
     *
     * @return array|mixed
     */
    public static function post($name = NULL, $defaultValue = NULL)
    {
        return static::request()->post($name, $defaultValue);
    }

    public static function getBoolean($val): bool
    {
        $v = Reqs::get($val);
        return SS::toBoolean($v);
    }

    /**
     * @param null $name
     * @param null $defaultValue
     *
     * @return array|mixed
     */
    public static function get($name = NULL, $defaultValue = NULL)
    {
        return static::request()->get($name, $defaultValue);
    }

    public static function postBoolean($val): bool
    {
        $v = Reqs::post($val);
        return SS::toBoolean($v);
    }

    /**
     * get trim(strip_tags($_GET[$name]));
     *
     * @param string $name
     * @param bool   $strip trim(strip_tags(
     *
     * @return string
     */
    public static function getString(string $name, bool $strip = true)
    {
        $val = self::get($name);
        if ($val == null) {
            return $val;
        }
        if (true === $strip) {
            $val = trim(strip_tags($val));
        }
        return $val;
    }

    /**
     * get (int)$_GET['id']  limit between 0 and 2147483647
     * @return int
     */
    public static function getId(): int
    {
        return self::getInteger('id');
    }

    /**
     * get (int)$_GET[$name]
     *
     * @param string $name
     * @param bool   $positive limit between 0 and 2147483647
     *
     * @return int
     */
    public static function getInteger(string $name, bool $positive = false): int
    {
        $val = (int)self::get($name);
        if (true === $positive) {
            if ($val <= NN::ZERO) {
                $val = NN::ZERO;
            }
            if ($val >= NN::MAX_INT) {
                $val = NN::MAX_INT;
            }
        }
        return $val;
    }

    /**
     * get page limit between 1 and 2147483647
     *
     * @param string $name
     *
     * @return int
     */
    public static function getPage(string $name = 'page'): int
    {
        $val = self::getInteger($name);
        if ($val <= 0) {
            $val = 1;
        }
        return $val;
    }

    /**
     * get (int)$_POST['id'] limit between 0 and 2147483647
     * @return int
     */
    public static function postId(): int
    {
        return self::postInteger('id');
    }

    /**
     * get (int)$_POST[$name]
     *
     * @param string $name
     * @param bool   $positive limit between 0 and 2147483647
     *
     * @return int
     */
    public static function postInteger(string $name, bool $positive = false): int
    {
        $val = (int)self::post($name);
        if (true === $positive) {
            if ($val <= NN::ZERO) {
                $val = NN::ZERO;
            }
            if ($val >= NN::MAX_INT) {
                $val = NN::MAX_INT;
            }
        }
        return $val;
    }
}