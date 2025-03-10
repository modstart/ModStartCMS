<?php


namespace Module\Member\Util;


use ModStart\Core\Assets\AssetsUtil;
use ModStart\Module\ModuleManager;
use Module\Member\Auth\MemberUser;
use Module\Member\Auth\MemberVip;

class ApiUtil
{
    public static function config()
    {
        $config = modstart_config();

        $data = [];

        // SSO
        $data['ssoClientEnable'] = $config->getBoolean('ssoClientEnable', false);

        // 登录
        $data['loginCaptchaEnable'] = $config->getBoolean('loginCaptchaEnable', false);
        $data['loginCaptchaProvider'] = $config->get('loginCaptchaProvider');
        $data['Member_LoginPhoneEnable'] = $config->getBoolean('Member_LoginPhoneEnable', false);
        $data['Member_LoginDefault'] = $config->get('Member_LoginDefault');

        // 注册
        $data['registerDisable'] = $config->getBoolean('registerDisable');
        $data['registerEmailEnable'] = $config->getBoolean('registerEmailEnable');
        $data['registerPhoneEnable'] = $config->getBoolean('registerPhoneEnable');
        $data['Member_RegisterDefault'] = $config->get('Member_RegisterDefault');
        $data['Member_RegisterCaptchaProvider'] = $config->get('Member_RegisterCaptchaProvider');
        $data['Member_RegisterPhoneEnable'] = $config->getBoolean('Member_RegisterPhoneEnable', false);
        $data['Member_RegisterPhonePasswordEnable'] = $config->getBoolean('Member_RegisterPhonePasswordEnable', false);
        $data['registerOauthEnable'] = $config->getBoolean('registerOauthEnable', false);

        // 授权登录
        $data['oauthWechatMobileEnable'] = $config->getBoolean('oauthWechatMobileEnable');
        $data['oauthQQEnable'] = $config->getBoolean('oauthQQEnable');
        $data['oauthWeiboEnable'] = $config->getBoolean('oauthWeiboEnable');
        $data['Member_OauthBindPhoneEnable'] = $config->getBoolean('Member_OauthBindPhoneEnable', false);
        $data['Member_OauthBindEmailEnable'] = $config->getBoolean('Member_OauthBindEmailEnable', false);

        // 找回密码
        $data['retrieveDisable'] = $config->getBoolean('retrieveDisable');
        $data['retrievePhoneEnable'] = $config->getBoolean('retrievePhoneEnable');
        $data['retrieveEmailEnable'] = $config->getBoolean('retrieveEmailEnable');

        // 账号安全
        $data['Member_ProfileEmailEnable'] = $config->getBoolean('Member_ProfileEmailEnable', false);
        $data['Member_ProfilePhoneEnable'] = $config->getBoolean('Member_ProfilePhoneEnable', false);
        $data['Member_DeleteEnable'] = $config->getBoolean('Member_DeleteEnable', false);

        // VIP
        $data['Member_VipEnable'] = ModuleManager::getModuleConfig('Member', 'vipEnable', false);

        // 资产
        $data['Member_CreditName'] = ModuleManager::getModuleConfig('Member', 'creditName', '积分');
        $data['Member_MoneyEnable'] = ModuleManager::getModuleConfig('Member', 'moneyEnable', false);
        $data['Member_MoneyChargeEnable'] = modstart_config('Member_MoneyChargeEnable', false);
        $data['Member_MoneyCashEnable'] = modstart_config('Member_MoneyCashEnable', false);
        $data['Member_CreditEnable'] = ModuleManager::getModuleConfig('Member', 'creditEnable', false);

        // 其他
        $data['Member_AgreementEnable'] = $config->getBoolean('Member_AgreementEnable', false);
        $data['Member_AgreementTitle'] = $config->get('Member_AgreementTitle');
        $data['Member_PrivacyEnable'] = $config->getBoolean('Member_PrivacyEnable', false);
        $data['Member_PrivacyTitle'] = $config->get('Member_PrivacyTitle');

        return $data;
    }

    public static function user()
    {
        $user = [
            'id' => 0,
            'avatar' => 'asset/image/avatar.svg',
            'avatarMedium' => 'asset/image/avatar.svg',
            'avatarBig' => 'asset/image/avatar.svg',
            'nickname' => '',
            'username' => '',
            'viewName' => '游客',
            'phone' => '',
            'phoneVerified' => false,
            'email' => '',
            'emailVerified' => false,
            'vip' => MemberVip::get(),
            'vipExpire' => null,
            'passwordEmpty' => false,
        ];
        if (MemberUser::isLogin()) {
            $memberUser = MemberUser::user();
            $user['id'] = $memberUser['id'];
            $user['avatar'] = $memberUser['avatar'] ? $memberUser['avatar'] : $user['avatar'];
            $user['avatarMedium'] = $memberUser['avatarMedium'] ? $memberUser['avatarMedium'] : $user['avatarMedium'];
            $user['avatarBig'] = $memberUser['avatarBig'] ? $memberUser['avatarBig'] : $user['avatarBig'];
            $user['username'] = $memberUser['username'];
            $user['nickname'] = $memberUser['nickname'];
            $user['viewName'] = MemberUtil::viewName($memberUser);
            $user['phone'] = $memberUser['phone'];
            $user['phoneVerified'] = !!$memberUser['phoneVerified'];
            $user['email'] = $memberUser['email'];
            $user['emailVerified'] = !!$memberUser['emailVerified'];
            $user['vip'] = MemberVip::get();
            $user['vipExpire'] = $memberUser['vipExpire'];
            $user['passwordEmpty'] = empty($memberUser['password']);
        }
        foreach (['avatar', 'avatarMedium', 'avatarBig',] as $k) {
            $user[$k] = AssetsUtil::fixFull($user[$k]);
        }
        return $user;
    }
}

