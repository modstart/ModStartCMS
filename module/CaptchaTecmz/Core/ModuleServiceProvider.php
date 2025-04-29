<?php

namespace Module\CaptchaTecmz\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use Module\CaptchaTecmz\Provider\TecmzCaptchaProvider;
use Module\Vendor\Provider\Captcha\CaptchaProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
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
                            'title' => '接口设置',
                            'children' => [
                                [
                                    'title' => '智能验证码',
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

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
