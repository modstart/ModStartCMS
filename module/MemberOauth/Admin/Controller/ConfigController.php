<?php

namespace Module\MemberOauth\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Input\Request;

class ConfigController extends Controller
{
    public function wechatMobile(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('微信手机授权登录');
        $builder->switch('oauthWechatMobileEnable', '开启微信授权登录');
        $builder->text('oauthWechatMobileAppId', 'AppId');
        $builder->text('oauthWechatMobileAppSecret', 'AppSecret');
        $builder->text('oauthWechatMobileProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function wechat(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('微信PC扫码登录');
        $builder->switch('oauthWechatEnable', '开启微信授权登录');
        $builder->text('oauthWechatAppId', 'AppId');
        $builder->text('oauthWechatAppSecret', 'AppSecret');
        $builder->text('oauthWechatProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function qq(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('QQ授权登录');
        $builder->switch('oauthQQEnable', '开启QQ授权登录')->help('回调地址请填写 <code>' . Request::domainUrl(true) . '/oauth_callback_qq</code>');
        $builder->text('oauthQQKey', 'APP ID');
        $builder->text('oauthQQAppSecret', 'APP KEY');
        $builder->text('oauthQQProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function weibo(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('微博授权登录');
        $builder->switch('oauthWeiboEnable', '开启微博授权登录')->help('回调地址请填写 <code>' . Request::domainUrl(true) . '/oauth_callback_weibo</code>');
        $builder->text('oauthWeiboKey', 'Key');
        $builder->text('oauthWeiboAppSecret', 'AppSecret');
        $builder->text('oauthWeiboProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function wechatMiniProgram(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('用户微信小程序');
        $builder->switch('oauthWechatMiniProgramEnable', '开启微信小程序登录');
        $builder->text('oauthWechatMiniProgramAppId', 'AppId');
        $builder->text('oauthWechatMiniProgramAppSecret', 'AppSecret');
        $builder->formClass('wide');
        return $builder->perform();
    }

}
