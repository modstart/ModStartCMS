<?php

namespace Module\SmsYunpian\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;
use Module\SmsYunpian\Provider\SmsSender;
use Module\Vendor\Provider\SmsSender\SmsSenderProvider;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        AdminMenu::register(function () {
            return [
                [
                    'title' => L('Site Manage'),
                    'icon' => 'cog',
                    'sort' => 400,
                    'children' => [
                        [
                            'title' => '短信配置',
                            'children' => [
                                [
                                    'title' => SmsSender::MODULE_TITLE . '短信',
                                    'url' => '\Module\\' . SmsSender::MODULE_NAME . '\Admin\Controller\ConfigController@setting',
                                ],
                            ]
                        ]
                    ]
                ]
            ];
        });
        if (modstart_config(SmsSender::MODULE_NAME . '_Enable', false)) {
            ModuleClassLoader::addNamespaceIfMissing('Overtrue\\EasySms', __DIR__ . '/../SDK/easy-sms');
            $this->app['config']->set('SmsSenderProvider', SmsSender::NAME);
            SmsSenderProvider::register(SmsSender::class);
        }
    }

    
    public function register()
    {

    }
}
