<?php

namespace Module\DataAliyunOssFe\Core;

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
                            'title' => '阿里云OSS存储(前端直传)',
                            'url' => '\Module\DataAliyunOssFe\Admin\Controller\ConfigController@index',
                        ],
                    ]
                ]
            ];
        });
        if (modstart_config('DataAliyunOssFe_Enable', false)) {
            $this->app['config']->set('DataStorageDriver', 'DataStorage_DataAliyunOssFe');
            DataStorageType::register('DataAliyunOssFe', '阿里云OSS云存储(前端直传)');
            $this->app->bind('DataStorage_DataAliyunOssFe', function () {
                $option = [];
                $option['accessKeyId'] = modstart_config()->getWithEnv('DataAliyunOssFe_AccessKeyId');
                $option['accessKeySecret'] = modstart_config()->getWithEnv('DataAliyunOssFe_AccessKeySecret');
                $option['endpoint'] = modstart_config()->getWithEnv('DataAliyunOssFe_Endpoint');
                $option['bucket'] = modstart_config()->getWithEnv('DataAliyunOssFe_Bucket');
                $storage = new DataAliyunOssFeDataStorage($option);
                $storage->init();
                return $storage;
            });
        }

        ModStartHook::subscribe('UploadScript', function ($param = []) {
            return RenderUtil::view('module::DataAliyunOssFe.View.inc.script', [
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
