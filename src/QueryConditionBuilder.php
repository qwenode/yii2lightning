<?php

namespace qwenode\yii2lightning;

class QueryConditionBuilder
{
    /**
     * $field <> $value
     * @param string $field
     * @param $value
     * @return array
     */
    public static function notEqual(string $field, $value): array
    {
        return ['<>', $field, $value];
    }

    /**
     * $field = $value
     * @param string $field
     * @param $value
     * @return array
     */
    public static function equal(string $field, $value): array
    {
        return [$field => $value];
    }

    /**
     * $field like $value
     * @param string $field
     * @param $value
     * @return array
     */
    public static function like(string $field, $value): array
    {
        return ['like', $field, $value];
    }

    /**
     * $field > $value
     * @param string $field
     * @param $value
     * @return array
     */
    public static function greaterThen(string $field, $value): array
    {
        return ['>', $field, $value];
    }

    /**
     * $field >= $value
     * @param string $field
     * @param $value
     * @return array
     */
    public static function greaterThenEqual(string $field, $value): array
    {
        return ['>=', $field, $value];
    }

    /**
     * $field < $value
     * @param string $field
     * @param $value
     * @return array
     */
    public static function lessThen(string $field, $value): array
    {
        return ['<', $field, $value];
    }

    /**
     * $field <= $value
     * @param string $field
     * @param $value
     * @return array
     */
    public static function lessThenEqual(string $field, $value): array
    {
        return ['<=', $field, $value];
    }
}