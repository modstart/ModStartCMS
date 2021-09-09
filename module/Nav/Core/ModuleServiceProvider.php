<?php

namespace Module\Nav\Core;

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
                        'title' => '导航配置',
                        'url' => '\Module\Nav\Admin\Controller\NavController@index',
                    ],
                ]
            ]
        ]);
    }

    
    public function register()
    {

    }
}
