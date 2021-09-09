<?php

namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Input\Request;
use ModStart\Form\Form;
use ModStart\Module\ModuleManager;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('功能设置');
        $builder->switch('loginCaptchaEnable', '启用登录验证码');
        $builder->switch('registerDisable', '禁用注册');
        $builder->switch('registerEmailEnable', '启用邮箱注册');
        $builder->switch('registerPhoneEnable', '启用手机注册');
        $builder->switch('retrieveDisable', '禁用找回密码');
        $builder->switch('retrievePhoneEnable', '启用手机找回密码');
        $builder->switch('retrieveEmailEnable', '启用邮箱找回密码');
        $builder->formClass('wide');
        return $builder->perform();
    }


    public function agreement(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('用户协议');
        $builder->switch('Member_AgreementEnable', '用户使用协议开启');
        $builder->text('Member_AgreementTitle', '用户使用协议标题')->help('默认为 用户使用协议');
        $builder->richHtml('Member_AgreementContent', '用户使用协议内容');
        $builder->formClass('wide');
        return $builder->perform();
    }


    public function vip(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('用户VIP设置');
        $builder->switch('moduleMemberVipEnable', '用户VIP功能开启');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function oauthWechatMobile(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('微信手机授权登录');
        $builder->switch('oauthWechatMobileEnable', '开启微信授权登录');
        $builder->text('oauthWechatMobileAppId', 'AppId');
        $builder->text('oauthWechatMobileAppSecret', 'AppSecret');
        $builder->text('oauthWechatMobileProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function oauthWechat(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('微信PC扫码登录');
        $builder->switch('oauthWechatEnable', '开启微信授权登录');
        $builder->text('oauthWechatAppId', 'AppId');
        $builder->text('oauthWechatAppSecret', 'AppSecret');
        $builder->text('oauthWechatProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function oauthWechatMiniProgram(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('微信小程序登录');
        $builder->switch('oauthWechatMiniProgramEnable', '开启微信小程序登录');
        $builder->text('oauthWechatMiniProgramAppId', 'AppId');
        $builder->text('oauthWechatMiniProgramAppSecret', 'AppSecret');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function oauthQQ(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('QQ授权登录');
        $builder->switch('oauthQQEnable', '开启QQ授权登录')->help('回调地址请填写 <code>' . Request::domainUrl(true) . '/oauth_callback_qq</code>');
        $builder->text('oauthQQKey', 'APP ID');
        $builder->text('oauthQQAppSecret', 'APP KEY');
        $builder->text('oauthQQAppSecretProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function oauthWeibo(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('微博授权登录');
        $builder->switch('oauthWeiboEnable', '开启微博授权登录')->help('回调地址请填写 <code>' . Request::domainUrl(true) . '/oauth_callback_weibo</code>');
        $builder->text('oauthWeiboKey', 'Key');
        $builder->text('oauthWeiboAppSecret', 'AppSecret');
        $builder->text('oauthWeiboProxy', '授权回调域名代理')->help('如不清楚此参数意义，请留空');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function money(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('用户资金设置');
        if (ModuleManager::getModuleConfigBoolean('Member', 'moneyCashEnable', false)) {
            $builder->switch('Member_MoneyCashEnable', '开启用户提现')->when('=', 1, function (Form $form) {
                $form->number('Member_MoneyCashMin', '最小提现金额')->help('默认为 100');
                $form->number('Member_MoneyCashTaxRate', '用户提现手续费')->help('如 1.00 表示手续费为 1.00%');
                $form->richHtml('Member_MoneyCashDescription', '用户提现说明');
            });
        }
        $builder->formClass('wide');
        return $builder->perform();
    }
}
