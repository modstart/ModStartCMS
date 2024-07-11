<?php

namespace Module\DataQcloudCosFe\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Hook\ModStartHook;
use ModStart\Core\Util\RenderUtil;
use ModStart\Data\DataStorageType;

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
                            'title' => '腾讯云COS(前端直传)',
                            'url' => '\Module\DataQcloudCosFe\Admin\Controller\ConfigController@index',
                        ],
                    ]
                ]
            ];
        });
        if (modstart_config('DataQcloudCosFe_Enable', false)) {
            $this->app['config']->set('DataStorageDriver', 'DataStorage_DataQcloudCosFe');
            DataStorageType::register('DataQcloudCosFe', '腾讯云COS（前端直传）');
            $this->app->bind('DataStorage_DataQcloudCosFe', function () {
                $option = [];
                $option['region'] = modstart_config('DataQcloudCosFe_Region');
                $option['secretId'] = modstart_config('DataQcloudCosFe_SecretId');
                $option['secretKey'] = modstart_config('DataQcloudCosFe_SecretKey');
                $option['bucket'] = modstart_config('DataQcloudCosFe_Bucket');
                $storage = new DataQcloudCosFeDataStorage($option);
                $storage->init();
                return $storage;
            });
        }
        ModStartHook::subscribe('UploadScript', function ($param = []) {
            return RenderUtil::view('module::DataQcloudCosFe.View.inc.script', [
                'param' => $param,
            ]);
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
