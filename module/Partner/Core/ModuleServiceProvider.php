<?php

namespace Module\Partner\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;
use Module\Partner\Biz\PartnerPositionBiz;
use Module\Vendor\Admin\Widget\AdminWidgetLink;

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
            ModuleClassLoader::addClass('MPartner', __DIR__ . '/../Helpers/MPartner.php');
        }

        PartnerPositionBiz::registerQuick('page', '独立页面');
        AdminWidgetLink::register(function () {
            $menu = [];
            $menu[] = ['友情链接', modstart_web_url('partner')];
            return AdminWidgetLink::build('系统', $menu);
        });

        AdminMenu::register(function () {
            return [
                [
                    'title' => '物料管理',
                    'icon' => 'description',
                    'sort' => 200,
                    'children' => [
                        [
                            'title' => '友情链接',
                            'url' => '\Module\Partner\Admin\Controller\PartnerController@index',
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
