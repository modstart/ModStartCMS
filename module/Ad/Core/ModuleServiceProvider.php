<?php

namespace Module\Ad\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Dao\ModelUtil;
use Module\Vendor\Admin\Config\AdminWidgetLink;

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
                        'title' => '广告位',
                        'url' => '\Module\Ad\Admin\Controller\AdController@index',
                    ],
                ]
            ]
        ]);
    }

    
    public function register()
    {

    }
}
