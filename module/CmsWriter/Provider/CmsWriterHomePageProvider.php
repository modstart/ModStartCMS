<?php


namespace Module\CmsWriter\Provider;


use Module\Vendor\Provider\HomePage\AbstractHomePageProvider;

class CmsWriterHomePageProvider extends AbstractHomePageProvider
{
    const ACTION = '\\Module\\CmsWriter\\Web\\Controller\\IndexController@index';

    public function title()
    {
        return '文章投稿系统';
    }

    public function action()
    {
        return self::ACTION;
    }

}
