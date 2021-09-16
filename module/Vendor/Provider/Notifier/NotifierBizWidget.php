<?php


namespace Module\Vendor\Provider\Notifier;


class NotifierBizWidget
{
    private static $list = [];

    public static function register($biz, $title)
    {
        self::$list[] = [
            'biz' => $biz,
            'title' => $title,
        ];
    }

    public static function get()
    {
        return self::$list;
    }
}
