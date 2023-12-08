<?php


namespace Module\Vendor\Type;


use ModStart\Core\Type\BaseType;

class AdminWidgetLinkType implements BaseType
{
    const WEB = 'web';
    const MOBILE = 'mobile';

    public static function getList()
    {
        return [
            self::WEB => '电脑端',
            self::MOBILE => '移动端',
        ];
    }

    public static function icon($value)
    {
        switch ($value) {
            case self::WEB:
                return 'fa fa-desktop';
            case self::MOBILE:
                return 'fa fa-mobile';
        }
        return '';
    }
}
