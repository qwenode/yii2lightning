<?php

namespace qwenode\yii2lightning;

class MenuBuilder
{
    /**
     * @var null|callable
     */
    public static $authorizeCallback = null;
    
    public static function item(string $label, $url, $authCode = null)
    {
        if (is_callable(self::$authorizeCallback) && $authCode !== null) {
            if (!call_user_func(self::$authorizeCallback, $authCode)) {
                return '';
            }
        }
        return [
            'label' => $label,
            'url'   => $url,
        ];
    }
    
    public static function items(string $label, ...$items)
    {
        $list = [
            'label' => $label,
            'items' => [],
        ];
        foreach ($items as $item) {
            if ($item == '') continue;
            $list['items'][] = $item;
        }
        return $list;
    }
}