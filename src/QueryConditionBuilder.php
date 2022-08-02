<?php

namespace qwenode\yii2lightning;

class QueryConditionBuilder
{
    public static function sortAsc(string $field): array
    {
        return [$field => SORT_ASC];
    }

    public static function sortDesc(string $field): array
    {
        return [$field => SORT_DESC];
    }

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

    /**
     * @param string $field
     * @param $list
     * @return array
     */
    public static function in(string $field, $list): array
    {
        return ['in', $field, $list];
    }

    /**
     * @param string $field
     * @param $list
     * @return array
     */
    public static function notIn(string $field, $list): array
    {
        return ['not in', $field, $list];
    }
}