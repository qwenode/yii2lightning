<?php

namespace qwenode\yii2lightning;

use ErrorException;
use yii\base\BaseObject;
use yii\base\Model;

/**
 * yii2 model helper
 */
class ModelHelper
{
    /**
     * get one model error message
     * @param Model $model
     * @return array|mixed|string
     */
    public static function getMessage(Model $model)
    {
        $message = '';
        if (!$model->hasErrors()) {
            return $message;
        }
        $messages = $model->getFirstErrors();
        if (is_array($messages)) {
            if (count($messages) != 0) {
                $message = array_values($messages)[0];
            }
        } else {
            $message = $messages;
        }
        return $message;
    }

    /**
     * @param Model $model
     * @throws ErrorException
     */
    public static function throwIfError(Model $model)
    {
        $message = self::getMessage($model);
        if ($message != '') {
            throw new ErrorException($message);
        }
    }

    /**
     *
     * @param $model
     * @throws ErrorException
     */
    public static function throwIfNull($model, $message = null)
    {
        if ($message == null) {
            $message = '数据不存在';
        }
        if ($model == null) {
            throw new ErrorException($message);
        }
        if ($message == '数据不存在') {
            $message = '对象必须继承Model';
        }
        if (!($model instanceof Model)) {
            throw new ErrorException($message);
        }
    }

    /**
     * @param $model
     * @param $property
     * @return mixed|string
     */
    public static function getPropertyValueOrZero($model, $property)
    {
        return self::getPropertyValue($model, $property, 0);
    }

    /**
     * @param $model
     * @param $property
     * @return mixed|string
     */
    public static function getPropertyValueOrNull($model, $property)
    {
        return self::getPropertyValue($model, $property, null);
    }

    /**
     * @param $model
     * @param $property
     * @return mixed|string
     */
    public static function getPropertyValueOrNullString($model, $property)
    {
        return self::getPropertyValue($model, $property, '');
    }

    /**
     * @param $model
     * @param $property
     * @param mixed $default
     * @return mixed|string
     */
    public static function getPropertyValue($model, $property, $default = '')
    {
        if (!$model instanceof BaseObject) {
            return $default;
        }
        return $model->{$property};
    }
}