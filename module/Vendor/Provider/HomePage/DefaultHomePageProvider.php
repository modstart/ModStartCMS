<?php


namespace Module\Vendor\Provider\HomePage;


class DefaultHomePageProvider extends AbstractHomePageProvider
{
    public function title()
    {
        return L('Default') . L('Home');
    }

    public function action()
    {
        return '\\App\\Web\\Controller\\IndexController@index';
    }

}
