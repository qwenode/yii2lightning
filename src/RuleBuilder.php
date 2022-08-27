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
    
    public static function numberRange(int $min, int $max, string ...$fields): array
    {
        return [$fields, MODEL_RULE_NUMBER, MODEL_RULE_NUMBER_MIN => $min, MODEL_RULE_NUMBER_MAX => $max];
    }
    
    public static function compare($field, $target): array
    {
        return [[$field], 'compare', 'compareAttribute' => $target];
    }
    
    public static function file($field, $extensions = ['png', 'jpg'], bool $skipOnEmpty = false)
    {
        return [[$field], 'file', 'skipOnEmpty' => $skipOnEmpty, 'extensions' => $extensions];
    }
}