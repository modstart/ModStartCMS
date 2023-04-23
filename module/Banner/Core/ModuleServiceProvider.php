<?php

namespace Module\Banner\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        if (method_exists(ModuleClassLoader::class, 'addClass')) {
            ModuleClassLoader::addClass('MBanner', __DIR__ . '/../Helpers/MBanner.php');
        }

        AdminMenu::register(function () {
            return [
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
            ];
        });
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
