<?php

namespace qwenode\yii2lightning;

use qwenode\yii2lightning\validators\DomainValidator;
use qwephp\AA;
use Yii;

/**
 * 校验规则
 */
class RuleBuilder
{
    /**
     * @param array $rule
     * @param string ...$scenarios
     * @return array
     */
    public static function onScenario(array $rule,string ...$scenarios)
    {
        $rule['on'] = $scenarios;
        return $rule;
    }
    
    /**
     * @param array $rule
     * @param string ...$scenarios
     * @return array
     */
    public static function exceptScenario(array $rule,string ...$scenarios)
    {
        $rule['except'] = $scenarios;
        return $rule;
    }
    
    /**
     * @param ...$fields
     * @return array
     */
    public static function boolean(string ...$fields): array
    {
        return [$fields, 'boolean'];
    }
    
    public static function dontValidate(...$fields): array
    {
        return [$fields, 'safe'];
    }
    
    public static function email(...$fields): array
    {
        return [$fields, 'email'];
    }
    
    public static function ip(...$fields): array
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
        return [$fields, self::INTEGER, 'min' => $min, 'max' => $max];
    }
    
    public static function number(string ...$fields): array
    {
        return [$fields, 'number'];
    }
    
    public static function default($defaultValue, ...$fields)
    {
        return [$fields, 'default', 'value' => $defaultValue];
    }
    
    public static function numberRange(int|float $min, int|float $max, string ...$fields): array
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
    
    public static function unique(...$fields): array
    {
        return [$fields, 'unique'];
    }
    
    public static function in(string $field, array $range, bool $strict = false): array
    {
        return [[$field], self::IN, 'range' => $range, 'strict' => $strict];
    }
    
    /**
     * 必须中文
     * @param ...$fields
     * @return array
     */
    public static function chinese(string $field, ?string $message = null): array
    {
        return self::pattern($field, '/([\x{4e00}-\x{9fa5}]+)/u', $message);
    }
    
    /**
     * 中国身份证判断
     * @param string $field
     * @param string|null $message
     * @return array
     */
    public static function chineseIDCard(string $field, ?string $message = null): array
    {
        return self::pattern($field,
            '/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/',
            $message);
    }
    
    /**
     * 中国手机号
     * @param $field
     * @param string|null $message
     * @return array
     */
    public static function chinesePhone($field, ?string $message = null): array
    {
        return self::pattern($field, '/^1[3-9]\d{9}$/', $message);
    }
    
    /**
     * 仅允许大小写字母和数字
     * @param $field
     * @param string|null $message
     * @return array
     */
    public static function alphabetAndNumber($field, ?string $message = null): array
    {
        return self::pattern($field, '/^[a-z0-9A-Z]+$/', $message);
    }
    /**
     * 仅允许大小写字母
     * @param $field
     * @param string|null $message
     * @return array
     */
    public static function alphabet($field, ?string $message = null): array
    {
        return self::pattern($field, '/^[a-zA-Z]+$/', $message);
    }
    
    public static function pattern(string $field, string $pattern, ?string $message = null)
    {
        $r = [[$field], 'match', 'pattern' => $pattern];
        if (AA::notNull($message)) {
            $r['message'] = $message;
        }
        return $r;
    }
    
    public static function inKey(string $field, array $range, bool $strict = false): array
    {
        return self::in($field, array_keys($range), $strict);
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
        if (AA::notNull($message)) {
            $r = self::withMessage($r, $message);
        }
        return $r;
    }
    
    /**
     * function($v){ return $v; }
     * @param callable $callable
     * @param ...$fields
     * @return array
     */
    public static function filter(callable $callable, ...$fields): array
    {
        return [$fields, self::FILTER, self::FILTER => $callable];
    }
    
    /**
     * url validation
     * @param ...$fields
     * @return array
     */
    public static function url(...$fields)
    {
        return [$fields, self::URL];
    }
    
    public static function stripTags(...$fields): array
    {
        return [$fields, self::FILTER, self::FILTER => 'strip_tags'];
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

    const URL = 'url';
    const REQUIRED = 'required';
    const FILE = 'file';
    const FILE_SKIP_ON_EMPTY = 'skipOnEmpty';
    const FILE_EXTENSIONS = 'extensions';
    const IN = 'in';
    const IN_RANGE = 'range';
    const STRING = 'string';
    const STRING_MIN = 'min';
    const STRING_MAX = 'max';
    CONST FILTER='filter';
    const DEFAULT = 'default';
    const INTEGER = 'integer';
    const NUMBER = 'number';
    const NUMBER_MIN = 'min';
    const NUMBER_MAX = 'max';
    const DEFAULT_VALUE = 'value';
    const UNIQUE = 'unique';
    const UNIQUE_TARGET_ATTRIBUTE = 'targetAttribute';
    const EMAIL = 'email';
    const WHEN = 'when';
    const INTEGER_MIN = 'min';
    const INTEGER_MAX = 'max';
    const BETWEEN = 'between';
    const TRIM = 'trim';
}