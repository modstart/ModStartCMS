<?php
/* @var \Illuminate\Routing\Router $router */
$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'member/agreement', 'PageController@agreement');
    $router->match(['get', 'post'], 'member/privacy', 'PageController@privacy');

    $router->match(['get', 'post'], 'login', 'AuthController@login');
    $router->match(['get', 'post'], 'login/captcha', 'AuthController@loginCaptcha');
    $router->match(['get', 'post'], 'login/phone', 'AuthController@loginPhone');
    $router->match(['get', 'post'], 'login/phone_captcha', 'AuthController@loginPhoneCaptcha');
    $router->match(['get', 'post'], 'login/phone_verify', 'AuthController@loginPhoneVerify');
    $router->match(['get', 'post'], 'login/sso', 'AuthController@loginSso');
    $router->match(['get', 'post'], 'login/other', 'AuthController@loginOther');
    $router->match(['get', 'post'], 'logout', 'AuthController@logout');
    $router->match(['get', 'post'], 'register', 'AuthController@register');
    $router->match(['get', 'post'], 'register/phone', 'AuthController@registerPhone');
    $router->match(['get', 'post'], 'register/captcha', 'AuthController@registerCaptcha');
    $router->match(['get', 'post'], 'register/captcha_verify', 'AuthController@registerCaptchaVerify');
    $router->match(['get', 'post'], 'register/phone_verify', 'AuthController@registerPhoneVerify');
    $router->match(['get', 'post'], 'register/email_verify', 'AuthController@registerEmailVerify');
    $router->match(['get', 'post'], 'retrieve', 'AuthController@retrieve');
    $router->match(['get', 'post'], 'retrieve/email', 'AuthController@retrieveEmail');
    $router->match(['get', 'post'], 'retrieve/email_verify', 'AuthController@retrieveEmailVerify');
    $router->match(['get', 'post'], 'retrieve/phone', 'AuthController@retrievePhone');
    $router->match(['get', 'post'], 'retrieve/phone_verify', 'AuthController@retrievePhoneVerify');
    $router->match(['get', 'post'], 'retrieve/captcha', 'AuthController@retrieveCaptcha');
    $router->match(['get', 'post'], 'retrieve/reset', 'AuthController@retrieveReset');

    $router->get('sso/client', 'AuthController@ssoClient');
    $router->get('sso/server', 'AuthController@ssoServer');
    $router->get('sso/server_success', 'AuthController@ssoServerSuccess');
    $router->get('sso/server_logout', 'AuthController@ssoServerLogout');
    $router->get('sso/client_logout', 'AuthController@ssoClientLogout');

    $router->match(['get', 'post'], 'oauth_proxy', 'AuthController@oauthProxy');
    $router->match(['get', 'post'], 'oauth_login_{oauthType}', 'AuthController@oauthLogin');
    $router->match(['get', 'post'], 'oauth_callback_{oauthType}', 'AuthController@oauthCallback');
    $router->match(['get', 'post'], 'oauth_bind_{oauthType}', 'AuthController@oauthBind');
    $router->match(['get', 'post'], 'oauth_proxy', 'AuthController@oauthProxy');
    $router->match(['get', 'post'], 'oauth_bind/captcha', 'AuthController@oauthBindCaptcha');
    $router->match(['get', 'post'], 'oauth_bind/captcha_verify', 'AuthController@oauthBindCaptchaVerify');
    $router->match(['get', 'post'], 'oauth_bind/email_verify', 'AuthController@oauthBindEmailVerify');
    $router->match(['get', 'post'], 'oauth_bind/phone_verify', 'AuthController@oauthBindPhoneVerify');

    $router->match(['get', 'post'], 'member', 'MemberController@index');

    $router->match(['get', 'post'], 'member_profile/captcha', 'MemberProfileController@captcha');
    $router->match(['get', 'post'], 'member_profile/security', 'MemberProfileController@security');
    $router->match(['get', 'post'], 'member_profile/password', 'MemberProfileController@password');
    $router->match(['get', 'post'], 'member_profile/profile', 'MemberProfileController@profile');
    $router->match(['get', 'post'], 'member_profile/avatar', 'MemberProfileController@avatar');
    $router->match(['get', 'post'], 'member_profile/bind', 'MemberProfileController@bind');
    $router->match(['get', 'post'], 'member_profile/email', 'MemberProfileController@email');
    $router->match(['get', 'post'], 'member_profile/email_verify', 'MemberProfileController@emailVerify');
    $router->match(['get', 'post'], 'member_profile/phone', 'MemberProfileController@phone');
    $router->match(['get', 'post'], 'member_profile/phone_verify', 'MemberProfileController@phoneVerify');
    $router->match(['get', 'post'], 'member_profile/oauth/{type}', 'MemberProfileController@oauth');
    $router->match(['get', 'post'], 'member_profile/delete', 'MemberProfileController@delete');
    $router->match(['get', 'post'], 'member_profile/nickname', 'MemberProfileController@nickname');

    $router->match(['get', 'post'], 'member_message', 'MemberMessageController@index');
    $router->match(['post'], 'member_message/delete', 'MemberMessageController@delete');
    $router->match(['get', 'post'], 'member_message/read', 'MemberMessageController@read');
    $router->match(['get', 'post'], 'member_message/read_all', 'MemberMessageController@readAll');

    $router->match(['get', 'post'], 'member_data/file_manager/{category}', 'MemberDataController@fileManager');
    $router->match(['get', 'post'], 'member_data/ueditor', 'MemberDataController@ueditor');
    $router->match(['get', 'post'], 'member_data/ueditor_guest', 'MemberDataController@ueditorGuest');

    $router->match(['get', 'post'], 'member_vip', 'MemberVipController@index');

    $router->match(['get', 'post'], 'member_money', 'MemberMoneyController@index');
    $router->match(['get', 'post'], 'member_money/cash', 'MemberMoneyCashController@index');
    $router->match(['get', 'post'], 'member_money/cash/log', 'MemberMoneyCashController@log');
    $router->match(['get', 'post'], 'member_money/charge', 'MemberMoneyChargeController@index');

    $router->match(['get', 'post'], 'member_credit', 'MemberCreditController@index');

    $router->match(['get', 'post'], 'member_address', 'MemberAddressController@index');
    $router->match(['get', 'post'], 'member_address/add', 'MemberAddressController@add');
    $router->match(['get', 'post'], 'member_address/edit', 'MemberAddressController@edit');
    $router->match(['post'], 'member_address/delete', 'MemberAddressController@delete');
    $router->match(['get', 'post'], 'member_address/area_china', 'MemberAddressController@areaChina');

});
