<?php

namespace Module\CaptchaTecmz\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;
use Module\CaptchaTecmz\Driver\CaptchaTecmzProvider;
use Module\CaptchaTecmz\Provider\TecmzCaptchaProvider;
use Module\SmsAliyun\Driver\SmsAliyunSender;
use Module\Vendor\Provider\Captcha\CaptchaProvider;

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
                            'title' => '验证服务',
                            'children' => [
                                [
                                    'title' => '魔众智能验证码',
                                    'url' => '\Module\CaptchaTecmz\Admin\Controller\ConfigController@setting',
                                ],
                            ]
                        ]
                    ]
                ]
            ];
        });
        CaptchaProvider::register(TecmzCaptchaProvider::class);
    }

    
    public function register()
    {

    }
}
