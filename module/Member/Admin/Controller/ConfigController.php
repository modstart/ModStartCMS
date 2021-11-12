<?php

namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Input\Request;
use ModStart\Form\Form;
use ModStart\Module\ModuleManager;
use Module\Vendor\Provider\Captcha\CaptchaProvider;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $captchaType = array_merge(['' => '默认'], CaptchaProvider::nameTitleMap());
        $builder->pageTitle('功能设置');
        $builder->switch('loginCaptchaEnable', '启用登录验证码')
            ->when('=', true, function (Form $form) use ($captchaType) {
                $form->select('loginCaptchaProvider', '登录验证码类型')->options($captchaType);
            });
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

    public function credit(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('积分设置');
        $builder->switch('Member_CreditEnable', '启用积分功能');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function money(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('用户资金设置');
        $builder->switch('Member_MoneyEnable', '启用钱包功能');
        if (ModuleManager::getModuleConfig('Member', 'moneyEnable', false)) {
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
