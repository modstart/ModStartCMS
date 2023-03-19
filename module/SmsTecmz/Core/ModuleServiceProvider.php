<?php

namespace Module\SmsTecmz\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;
use Module\SmsAliyun\Driver\SmsAliyunSender;
use Module\SmsTecmz\Driver\SmsTecmzSender;
use Module\SmsTecmz\Provider\SmsSender;
use Module\Vendor\Provider\SmsSender\SmsSenderProvider;

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
                'title' => L('Site Manage'),
                'icon' => 'cog',
                'sort' => 400,
                'children' => [
                    [
                        'title' => '短信邮箱',
                        'children' => [
                            [
                                'title' => '魔众短信',
                                'url' => '\Module\SmsTecmz\Admin\Controller\ConfigController@setting',
                            ],
                        ]
                    ]
                ]
            ]
        ]);
        if (modstart_config('SmsTecmz_Enable', false)) {
            $this->app['config']->set('SmsSenderProvider', 'tecmz');
            SmsSenderProvider::register(SmsSender::class);
        }
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
