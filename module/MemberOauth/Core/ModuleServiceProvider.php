<?php

namespace Module\MemberOauth\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;
use ModStart\Module\ModuleManager;
use Module\Member\Config\MemberOauth;
use Module\MemberOauth\Oauth\QqOauth;
use Module\MemberOauth\Oauth\WechatMobileOauth;
use Module\MemberOauth\Oauth\WechatOauth;
use Module\MemberOauth\Oauth\WeiboOauth;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        AdminMenu::register([
            [
                'title' => '功能设置',
                'icon' => 'tools',
                'sort' => 300,
                'children' => [
                    [
                        'title' => '用户授权登录',
                        'children' => [
                            [
                                'title' => '微信授权登录',
                                'url' => '\Module\MemberOauth\Admin\Controller\ConfigController@wechatMobile',
                            ],
                            [
                                'title' => '微信扫码登录',
                                'url' => '\Module\MemberOauth\Admin\Controller\ConfigController@wechat',
                            ],
                            [
                                'title' => 'QQ授权登录',
                                'url' => '\Module\MemberOauth\Admin\Controller\ConfigController@qq',
                            ],
                            [
                                'title' => '微博授权登录',
                                'url' => '\Module\MemberOauth\Admin\Controller\ConfigController@weibo',
                            ],
                            ModuleManager::getModuleConfigBoolean('MemberOauth', 'wechatMiniProgramEnable', false)
                                ? [
                                'title' => '微信小程序',
                                'url' => '\Module\MemberOauth\Admin\Controller\ConfigController@wechatMiniProgram',
                            ] : null,
                        ]
                    ],
                ]
            ]
        ]);

        ModuleClassLoader::addNamespace('Overtrue\\Socialite', __DIR__ . '/../SDK/socialite/src');
        MemberOauth::register(function () {
            $list = [];
            if (modstart_config('oauthWechatMobileEnable', false)) {
                $list[] = new WechatMobileOauth();
            }
            if (modstart_config('oauthWechatEnable', false)) {
                $list[] = new WechatOauth();
            }
            if (modstart_config('oauthQQEnable', false)) {
                $list[] = new QqOauth();
            }
            if (modstart_config('oauthWeiboEnable', false)) {
                $list[] = new WeiboOauth();
            }
            return $list;
        });
    }

    
    public function register()
    {

    }
}
