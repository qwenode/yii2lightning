<?php

namespace qwenode\yii2lightning;

/**
 * 校验规则
 */
class RuleBuilder
{
    public static function required(string ...$fields): array
    {
        return [$fields, MODEL_RULE_REQUIRED];
    }
    
    public static function trim(string ...$fields): array
    {
        return [$fields, MODEL_RULE_TRIM];
    }
    
    public static function string(string ...$fields): array
    {
        return [$fields, MODEL_RULE_STRING];
    }
    
    public static function stringLength(int $min, int $max, string ...$fields): array
    {
        return [$fields, MODEL_RULE_STRING, MODEL_RULE_STRING_MIN => $min, MODEL_RULE_STRING_MAX => $max];
    }
    
    public static function integer(string ...$fields): array
    {
        return [$fields, MODEL_RULE_INTEGER];
    }
    
    public static function integerRange(int $min, int $max, string ...$fields): array
    {
        return [$fields, MODEL_RULE_INTEGER, MODEL_RULE_INTEGER_MIN => $min, MODEL_RULE_INTEGER_MAX => $max];
    }
    
    public static function number(string ...$fields): array
    {
        return [$fields, MODEL_RULE_NUMBER];
    }
    
    public static function default($defaultValue, ...$fields)
    {
        return [$fields, 'default', 'value' => $defaultValue];
    }
    
    public static function numberRange(int $min, int $max, string ...$fields): array
    {
        return [$fields, MODEL_RULE_NUMBER, MODEL_RULE_NUMBER_MIN => $min, MODEL_RULE_NUMBER_MAX => $max];
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
    public static function withMessage($rule, string $msg)
    {
        if (!is_array($rule)) {
            return $rule;
        }
        $rule['message'] = $msg;
        return $rule;
    }
}