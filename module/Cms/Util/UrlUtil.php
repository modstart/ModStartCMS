<?php


namespace Module\Cms\Util;


class UrlUtil
{
    public static function tag($name)
    {
        return modstart_web_url('tag/' . htmlspecialchars($name));
    }
}
