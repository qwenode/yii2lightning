<?php

namespace qwenode\yii2lightning;

class ViewBuilder
{
    public static function inputDate($unixtime, ...$options): array
    {
        return ['type' => 'date', 'value' => LightningHelper::asDate($unixtime), ...$options];
    }
    
}