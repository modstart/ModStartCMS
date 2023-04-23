<?php

namespace Module\Nav\Core;

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
            ModuleClassLoader::addClass('MNav', __DIR__ . '/../Helpers/MNav.php');
        }

        AdminMenu::register(function () {
            return [
                [
                    'title' => '物料管理',
                    'icon' => 'description',
                    'sort' => 200,
                    'children' => [
                        [
                            'title' => '导航设置',
                            'url' => '\Module\Nav\Admin\Controller\NavController@index',
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
