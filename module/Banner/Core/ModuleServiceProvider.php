<?php

namespace Module\Banner\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;
use ModStart\Module\ModuleManager;
use Module\Banner\Provider\BannerPositionProvider;
use Module\SmsAliyun\Driver\SmsAliyunSender;
use Module\SmsTecmz\Driver\SmsTecmzSender;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
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

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
