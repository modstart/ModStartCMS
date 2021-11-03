<?php


namespace Module\Cms\Provider;


use Module\Vendor\Provider\HomePage\AbstractHomePageProvider;

class CmsHomePageProvider extends AbstractHomePageProvider
{
    const ACTION = '\\Module\\Cms\\Web\\Controller\\IndexController@index';

    public function title()
    {
        return 'CMS';
    }

    public function action()
    {
        return self::ACTION;
    }

}
