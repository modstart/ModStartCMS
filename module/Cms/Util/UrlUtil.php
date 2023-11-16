<?php


namespace Module\Cms\Util;

use Module\Cms\Type\ContentUrlMode;

class UrlUtil
{
    public static function tag($name)
    {
        return modstart_web_url('tag/' . htmlspecialchars($name));
    }

    public static function content($content)
    {
        if (!empty($content['fullUrl'])) {
            return modstart_web_url($content['fullUrl']);
        }
        if (empty($content['alias'])) {
            $content['alias'] = $content['id'];
        }
        $url = modstart_web_url('a/' . $content['alias']);
        if (modstart_config('Cms_ContentUrlMode') == ContentUrlMode::CAT) {
            $cat = CmsCatUtil::get($content['catId']);
            if (!empty($cat['url'])) {
                $url = modstart_web_url($cat['url'] . '/' . $content['alias']);
            }
        }
        return $url;
    }
}
