<?php

namespace qwenode\yii2lightning;

use qwephp\assert\Assertion;
use qwephp\StrHelper;
use yii\base\InvalidConfigException;

class ViewBuilder
{
    
    /**
     * @var null|callable
     */
    public static $authorizeCallback = null;
    
    public static function menuItem(string $label, $url, $authCode = null)
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
    
    public static function menuItems(string $label, ...$items)
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
    
    /**
     * 链接自动高亮
     * @param string $url
     * @param string $appendClass
     * @param string $activeClass
     * @return string
     * @throws InvalidConfigException
     */
    public static function activeLink(string $url, array $match = [], string $appendClass = '', string $activeClass = 'active')
    {
        $current = LightningHelper::getRequest()->getUrl();
        $tpl     = ' href="%s" class="%s" ';
        $match[] = $url;
        if (StrHelper::containArray($current, $match)) {
            return sprintf($tpl, $url, $appendClass . ' ' . $activeClass);
        }
        return sprintf($tpl, $url, $appendClass);
    }
    
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