<?php

namespace qwenode\yii2lightning;

use qwephp\assert\Assertion;

class ViewBuilder
{
    public static function inputDate($unixtime, ...$options): array
    {
        if ($unixtime <= 0) {
            $format = '';
        } else {
            $format = LightningHelper::asDate($unixtime);
        }
        return ['type' => 'date', 'value' => $format, ...$options];
    }
    
    public static function gridViewAttribute(string $attribute, callable $callable, ?string $format = null)
    {
        $attr = [
            'attribute' => $attribute,
            'value'     => $callable,
        ];
        if (Assertion::notNull($format)) {
            $attr['format'] = $format;
        }
        return $attr;
    }
    
    public static function gridViewLabel(string $label, callable $callable, ?string $format = null)
    {
        $attr = [
            'label' => $label,
            'value' => $callable,
        ];
        if (Assertion::notNull($format)) {
            $attr['format'] = $format;
        }
        return $attr;
    }
}