<?php


namespace Module\Cms\Type;


use ModStart\Core\Type\BaseType;
use Module\Cms\Util\CmsCatUtil;

class ContentUrlMode implements BaseType
{
    const A = 'a';
    const CAT = 'cat';

    public static function getList()
    {
        return [
            self::A => '/a/{id}',
            self::CAT => '/栏目URL/{id}',
        ];
    }

    public static function url($content)
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
