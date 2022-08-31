<?php

namespace qwenode\yii2lightning;

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
    
}