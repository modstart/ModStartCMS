<?php

namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Form\Form;
use ModStart\Module\ModuleManager;
use ModStart\Support\Concern\HasFields;
use Module\Member\Model\MemberDataStatistic;
use Module\Member\Util\MemberParamUtil;
use Module\Vendor\Provider\Captcha\CaptchaProvider;

class ConfigController extends Controller
{
    public static $PermitMethodMap = [
        'param' => '*',
        'dataStatistic' => '\\Module\\Member\\Admin\\Controller\\MemberController@index',
    ];

    public function setting(AdminConfigBuilder $builder)
    {
        $captchaType = array_merge(['' => '默认'], CaptchaProvider::nameTitleMap());
        $builder->pageTitle('注册登录');
        $builder->layoutPanel('登录', function ($builder) use ($captchaType) {
            /** @var HasFields $builder */
            $builder->switch('loginCaptchaEnable', '启用登录验证码')
                ->when('=', true, function (Form $form) use ($captchaType) {
                    $form->select('loginCaptchaProvider', '登录验证码类型')->options($captchaType)
                        ->help('用于 用户名密码登录（/login）、手机快捷登录（/login/phone） 的人机验证');
                });
            $builder->switch('Member_LoginPhoneEnable', '启用手机快捷登录')
                ->when('=', true, function (Form $form) {
                    $form->text('Member_LoginPhoneNameSuggest', '手机快捷登录用户名')
                        ->defaultValue('用户')
                        ->help(join('', [
                            '<p>默认为"用户"，用户注册后自动设置用户名和昵称为 "用户xxxx"。</p>',
                            '<p>可使用占位符，如"用户{Phone}"表示注册后自动设置为"用户+手机号"，用户名如遇冲突将会自动追加随机字符串。</p>',
                            '<p>其他占位符：{Phone}用户手机、{Phone4}手机后4位、{Uid}用户ID</p>',
                        ]));
                    $form->switch('Member_LoginPhoneAutoRegister', '手机快捷登录自动注册')
                        ->help('开启后，手机快捷登录遇用户不存在则自动注册，关闭表示手机号不存在不能登录');
                })
                ->help('开启手机快捷登录（/login/phone）');
            $loginDefaultOptions = [
                'default' => '用户名密码登录 /login',
                'phone' => '手机快捷登录 /login/phone',
            ];
            if (modstart_config('ssoClientEnable', false)) {
                $loginDefaultOptions['sso'] = 'SSO单点登录 /login/sso';
            }
            if (modstart_module_enabled('MemberWechatMpLogin')) {
                $loginDefaultOptions['other'] = '扫码/其他登录方式 /login/other';
            }
            $builder->select('Member_LoginDefault', '默认登录方式')->options($loginDefaultOptions);

        });
        $builder->layoutPanel('注册', function ($builder) use ($captchaType) {
            /** @var HasFields $builder */
            $builder->select('Member_RegisterCaptchaProvider', '注册验证码类型')->options($captchaType)
                ->help('用于 普通注册（/register）、手机快捷注册（/register_phone） 的人机验证');
            $builder->switch('registerDisable', '禁用注册')
                ->when('!=', true, function ($builder) {
                    $builder->switch('registerEmailEnable', '注册时填写邮箱');
                    $builder->switch('registerPhoneEnable', '注册时填写手机');
                    $builder->switch('Member_RegisterPhoneEnable', '启用手机快捷注册')->help('开启后用户可通过手机验证码快捷注册（/register_phone）');
                    $builder->select('Member_RegisterDefault', '默认注册方式')->options([
                        'default' => '用户名密码注册',
                        'phone' => '手机快捷注册',
                    ]);
                })
                ->when('=', true, function ($builder) {
                    $builder->switch('registerOauthEnable', '允许以授权方式注册');
                });
            if (modstart_module_enabled('MemberOauth')) {
                $builder->switch('Member_OauthBindPhoneEnable', '授权登录需绑定手机');
                $builder->switch('Member_OauthBindEmailEnable', '授权登录需绑定邮箱');
            }
            $builder->number('Member_UsernameMinLength', '用户名最小长度')->defaultValue(3);

        });
        $builder->layoutPanel('找回密码', function ($builder) {
            $builder->switch('retrieveDisable', '禁用找回密码')
                ->when('!=', true, function ($builder) {
                    $builder->switch('retrievePhoneEnable', '启用手机找回密码');
                    $builder->switch('retrieveEmailEnable', '启用邮箱找回密码');
                });
        });
        $builder->layoutPanel('账号安全', function ($builder) {
            $builder->switch('Member_ProfileEmailEnable', '开启邮箱绑定')->help('启用后用户中心增加邮箱绑定页面');
            $builder->switch('Member_ProfilePhoneEnable', '开启手机绑定')->help('启用后用户中心增加手机绑定页面');
            $builder->switch('Member_LoginRedirectCheckEnable', '登录后跳转安全验证')
                ->when('=', true, function ($builder) {
                    /** @var $builder HasFields */
                    $builder->textarea('Member_LoginRedirectWhiteList', '白名单')->placeholder('请输入域名白名单，每行一个，如：www.example.com');
                });
            $builder->switch('Member_DeleteEnable', '启用自助注销账号')
                ->help('开启后，用户中心可自主申请注销账号。用户注销账号后，用户名会重置为随机字符串，已绑定的手机、邮箱均会解绑');

        });
        if (ModuleManager::getModuleConfig('Member', 'dataStatisticEnable', false)) {
            $builder->layoutPanel('存储上传', function ($builder) {
                /** @var $builder HasFields */
                $builder->switch('Member_DataStatisticEnable', '开启上传限制')
                    ->when('=', true, function ($builder) {
                        /** @var $builder HasFields */
                        $builder->number('Member_DataStatisticDefaultLimit', '默认空间大小')
                            ->help('用户的默认空间大小，单位MB，默认为1024')
                            ->defaultValue(1024);
                    });
            });
        }
        $builder->formClass('wide');
        $builder->contentFixedBottomContentSave();
        $builder->disableBoxWrap(true);
        $builder->showReset(false)->showSubmit(false);
        return $builder->perform();
    }


    public function agreement(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('用户协议');
        $builder->switch('Member_AgreementEnable', '用户使用协议开启')
            ->when('=', true, function ($builder) {
                $builder->text('Member_AgreementTitle', '用户使用协议标题')->help('默认为 用户使用协议');
                $builder->richHtml('Member_AgreementContent', '用户使用协议内容');
            });

        $builder->switch('Member_PrivacyEnable', '用户隐私协议开启')
            ->when('=', true, function ($builder) {
                $builder->text('Member_PrivacyTitle', '用户隐私协议标题')->help('默认为 用户隐私协议');
                $builder->richHtml('Member_PrivacyContent', '用户隐私协议内容');
            });
        $builder->formClass('wide');
        return $builder->perform();
    }


    public function money(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('钱包设置');
        $builder->switch('Member_MoneyCashEnable', '开启用户提现')->when('=', 1, function (Form $form) {
            $form->number('Member_MoneyCashMin', '最小提现金额')->help('默认为 100');
            $form->number('Member_MoneyCashTaxRate', '用户提现手续费')->help('如 1.00 表示手续费为 1.00%');
            $form->richHtml('Member_MoneyCashDescription', '用户提现说明');
        });
        $builder->switch('Member_MoneyChargeEnable', '开启钱包充值');
        $builder->richHtml('Member_MoneyChargeDesc', '钱包充值说明');
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function message(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('消息设置');
        $builder->richHtml('Member_Registered_Message', '注册成功站内信')->help(MemberParamUtil::paramTitle());
        $builder->text('Member_Registered_EmailTitle', '注册成功邮件标题')->help(MemberParamUtil::paramTitle());
        $builder->richHtml('Member_Registered_Email', '注册成功邮件')->help(MemberParamUtil::paramTitle());
        $builder->formClass('wide');
        return $builder->perform();
    }

    public function dataStatistic()
    {
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $memberUserId = $input->getInteger('memberUserId');
        $data = [];
        $data['sizeLimit'] = $input->getInteger('sizeLimit');
        MemberDataStatistic::updateMemberUser($memberUserId, $data);
        return Response::generateSuccess('保存成功');
    }

    public function param()
    {
        return view('module::Member.View.admin.config.param');
    }
}
