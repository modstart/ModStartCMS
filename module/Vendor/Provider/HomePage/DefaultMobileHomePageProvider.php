<?php


namespace Module\Vendor\Provider\HomePage;


class DefaultMobileHomePageProvider extends AbstractHomePageProvider
{
    public function type()
    {
        return self::TYPE_MOBILE;
    }

    public function title()
    {
        return L('Default') . L('Home');
    }

    public function action()
    {
        return '\\App\\Web\\Controller\\IndexController@index';
    }

}
