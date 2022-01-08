<?php


namespace Module\Cms\Type;


class CatUrlMode
{
    public static function url($cat)
    {
        if (!empty($cat['fullUrl'])) {
            return modstart_web_url($cat['fullUrl']);
        }
        return modstart_web_url($cat['url']);
    }
}