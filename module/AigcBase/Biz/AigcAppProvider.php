<?php

namespace Module\AigcBase\Biz;

use Module\Vendor\Provider\ProviderTrait;

/**
 * @method static AbstractAigcAppProvider[] listAll()
 */
class AigcAppProvider
{
    use ProviderTrait;

    public static function registerQuick($name, $title, $url, $icon = 'fa fa-app', $param = [])
    {
        $provider = new QuickAigcAppProvider();
        $provider->name = $name;
        $provider->title = $title;
        $provider->url = $url;
        $provider->icon = $icon;
        if (!empty($param['image'])) {
            $provider->image = $param['image'];
        }
        if (!empty($param['order'])) {
            $provider->order = $param['order'];
        }
        self::register($provider);
    }
}
