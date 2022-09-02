<?php

namespace qwenode\yii2lightning;

use qwephp\assert\Assertion;
use qwephp\StrHelper;
use yii\base\InvalidConfigException;

class ViewBuilder
{
    /**
     * 链接自动高亮
     * @param string $url
     * @param string $appendClass
     * @param string $activeClass
     * @return string
     * @throws InvalidConfigException
     */
    public static function activeLink(string $url, string $appendClass = '', string $activeClass = 'active')
    {
        $current = LightningHelper::getRequest()->getUrl();
        $tpl     = ' href="%s" class="%s" ';
        if (StrHelper::contain($current, $url)) {
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