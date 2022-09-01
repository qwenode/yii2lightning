<?php

namespace qwenode\yii2lightning;

use qwenode\yii2lightning\validators\DomainValidator;
use qwephp\assert\Assertion;
use Yii;

/**
 * 校验规则
 */
class RuleBuilder
{
    
    public static function dontValidate(...$fields): array
    {
        return [$fields, 'safe'];
    }
    
    public static function ip(...$fields)
    {
        return [$fields, 'ip'];
    }
    
    public static function safe(...$fields): array
    {
        return self::dontValidate(...$fields);
    }
    
    public static function required(string ...$fields): array
    {
        return [$fields, 'required'];
    }
    
    public static function trim(string ...$fields): array
    {
        return [$fields, 'trim'];
    }
    
    public static function string(string ...$fields): array
    {
        return [$fields, 'string'];
    }
    
    public static function date($field, $format = 'php:Y-m-d')
    {
        return [[$field], 'date', 'timestampAttribute' => $field, 'defaultTimeZone' => Yii::$app->timeZone, 'format' => $format, 'min' => 10000000];
    }
    
    public static function stringLength(int $min, int $max, string ...$fields): array
    {
        return [$fields, 'string', 'min' => $min, 'max' => $max];
    }
    
    public static function integer(string ...$fields): array
    {
        return [$fields, 'integer'];
    }
    
    public static function integerRange(int $min, int $max, string ...$fields): array
    {
        return [$fields, 'integer', 'min' => $min, 'max' => $max];
    }
    
    public static function number(string ...$fields): array
    {
        return [$fields, 'number'];
    }
    
    public static function default($defaultValue, ...$fields)
    {
        return [$fields, 'default', 'value' => $defaultValue];
    }
    
    public static function numberRange(int $min, int $max, string ...$fields): array
    {
        return [$fields, 'number', 'min' => $min, 'max' => $max];
    }
    
    public static function compare($field, $target): array
    {
        return [[$field], 'compare', 'compareAttribute' => $target];
    }
    
    /**
     * @param $field
     * @param $extensions
     * @param bool $skipOnEmpty
     * @param bool $checkExtensionByMimeType 注意如果是csv,要设置为false, 因为https://stackoverflow.com/questions/40797023/yii2-setting-rules-for-csv-file-upload-and-retaining-original-filename
     * @return array
     */
    public static function file($field, $extensions = ['png', 'jpg'], bool $skipOnEmpty = false, bool $checkExtensionByMimeType = true)
    {
        return [[$field], 'file', 'skipOnEmpty' => $skipOnEmpty, 'extensions' => $extensions, 'checkExtensionByMimeType' => $checkExtensionByMimeType];
    }
    
    public static function unique(...$fields)
    {
        return [$fields, 'unique'];
    }
    
    public static function when($rule, ?callable $when, string $whenClient = '')
    {
        if (!is_array($rule)) {
            return $rule;
        }
        if (is_callable($when)) {
            $rule['when'] = $when;
        }
        if ($whenClient != '') {
            $rule['whenClient'] = $whenClient;
        }
        return $rule;
    }
    
    /**
     * 为规则附加错误消息
     * @param $rule
     * @param string $msg
     * @return mixed
     */
    public static function withMessage($rule, string $msg): array
    {
        if (!is_array($rule)) {
            return $rule;
        }
        $rule['message'] = $msg;
        return $rule;
    }
    
    public static function withAttribute($rule, array $params)
    {
        if (!is_array($rule)) {
            return $rule;
        }
        return array_merge($rule, $params);
    }
    
    public static function domain(string $field, ?string $message = null): array
    {
        $r = [[$field], DomainValidator::class];
        if (Assertion::notNull($message)) {
            $r = self::withMessage($r, $message);
        }
        return $r;
    }
    
    public static function filter(callable $callable, ...$fields): array
    {
        return [$fields, 'filter', 'filter' => $callable];
    }
    
    public static function filterToUnixTime(...$fields): array
    {
        
        return self::filter(function ($value) {
            $strtotime = strtotime($value);
            if ($strtotime < 1000000) {
                return 0;
            }
            return $strtotime;
        }, ...$fields);
    }
}