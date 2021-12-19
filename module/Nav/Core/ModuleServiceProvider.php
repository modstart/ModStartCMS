<?php

namespace Module\Nav\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        require_once(__DIR__ . '/../Helpers/MNav.php');

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

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
