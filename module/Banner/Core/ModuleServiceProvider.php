<?php

namespace Module\Banner\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;
use Module\SmsAliyun\Driver\SmsAliyunSender;
use Module\SmsTecmz\Driver\SmsTecmzSender;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        AdminMenu::register([
            [
                'title' => '物料管理',
                'icon' => 'description',
                'sort' => 200,
                'children' => [
                    [
                        'title' => '轮播图片',
                        'url' => '\Module\Banner\Admin\Controller\BannerController@index',
                    ],
                ]
            ]
        ]);
    }

    
    public function register()
    {

    }
}
