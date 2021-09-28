<?php


namespace Module\Vendor\Provider\HomePage;


class DefaultHomePageProvider extends AbstractHomePageProvider
{
    public function title()
    {
        return L('Default');
    }

    public function action()
    {
        return '\\App\\Web\\Controller\\IndexController@index';
    }

}
