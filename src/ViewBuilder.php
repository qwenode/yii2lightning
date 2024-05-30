<?php

namespace qwenode\yii2lightning;

use qwephp\AA;
use qwephp\SS;
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
        if (SS::containArray($current, $match)) {
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
    
    public static function gridViewAttribute(string $attribute, callable $callable, ?string $format = null, ?string $label = null)
    {
        $attr = [
            'attribute' => $attribute,
            'value'     => $callable,
        ];
        if ($label != null) {
            $attr['label'] = $label;
        }
        if (AA::notNull($format)) {
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
        if (AA::notNull($format)) {
            $attr['format'] = $format;
        }
        return $attr;
    }


//适用于GridView 与 DetailView 快速输入
    const ATTRIBUTE = 'attribute';
    const VALUE = 'value';
    const LABEL = 'label';

    const FORMAT = 'format';
    const FORMAT_RAW = 'raw';
    const FORMAT_HTML = 'html';
    const FORMAT_TEXT = 'text';
    const FORMAT_NTEXT = 'ntext';
    /**
     * 这个值被格式化为包含在 <p> 标签中的 HTML 编码的文本段落。
     */
    const FORMAT_PARAGRAPHS = 'paragraphs';
    const FORMAT_EMAIL = 'email';
    const FORMAT_IMAGE = 'image';
    const FORMAT_URL = 'url';
    const FORMAT_BOOLEAN = 'boolean';

    const OPTIONS = 'options';
    const DROPDOWN_PROMPT_ALL = [
        'prompt' => '全部',
    ];
    const DROPDOWN_PROMPT_CHOOSE = [
        'prompt' => '请选择',
    ];
    const DROPDOWN_PROMPT_NULL = [
        'prompt' => '默认空',
    ];
    const PLACEHOLDER = 'placeholder';

    const ENCTYPE = 'enctype';
    const ENCTYPE_MULTIPART_FORM_DATA = 'multipart/form-data';
    const FORM_UPLOAD_OPTIONS = [
        self::ENCTYPE => self::ENCTYPE_MULTIPART_FORM_DATA,
    ];//ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']])

}