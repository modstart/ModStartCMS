<?php


namespace Module\Nav\Type;


use ModStart\Core\Type\BaseType;

class NavOpenType implements BaseType
{
    const CURRENT_WINDOW = 1;
    const NEW_BLANK = 2;

    public static function getList()
    {
        return [
            self::CURRENT_WINDOW => '当前窗口',
            self::NEW_BLANK => '新窗口',
        ];
    }

    public static function getBlankAttributeFromValue($nav)
    {
        if (empty($nav)) {
            return '';
        }
        if (is_array($nav)) {
            $nav = isset($nav['openType']) ? $nav['openType'] : null;
        }
        switch ($nav) {
            case self::NEW_BLANK:
                return 'target="_blank"';
        }
        return '';
    }
}
