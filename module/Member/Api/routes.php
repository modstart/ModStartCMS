<?php

$router->group([
    'middleware' => [
        \Module\Member\Middleware\ApiAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'login', 'AuthController@login');
    $router->match(['get', 'post'], 'logout', 'AuthController@logout');
    $router->match(['get', 'post'], 'login_captcha', 'AuthController@loginCaptcha');
    $router->match(['get', 'post'], 'register', 'AuthController@register');
    $router->match(['get', 'post'], 'register_email_verify', 'AuthController@registerEmailVerify');
    $router->match(['get', 'post'], 'register_phone_verify', 'AuthController@registerPhoneVerify');
    $router->match(['get', 'post'], 'register_captcha', 'AuthController@registerCaptcha');
    $router->match(['get', 'post'], 'register_captcha_verify', 'AuthController@registerCaptchaVerify');
    $router->match(['get', 'post'], 'retrieve_phone', 'AuthController@retrievePhone');
    $router->match(['get', 'post'], 'retrieve_phone_verify', 'AuthController@retrievePhoneVerify');
    $router->match(['get', 'post'], 'retrieve_email', 'AuthController@retrieveEmail');
    $router->match(['get', 'post'], 'retrieve_email_verify', 'AuthController@retrieveEmailVerify');
    $router->match(['get', 'post'], 'retrieve_captcha', 'AuthController@retrieveCaptcha');
    $router->match(['get', 'post'], 'retrieve_reset_info', 'AuthController@retrieveResetInfo');
    $router->match(['get', 'post'], 'retrieve_reset', 'AuthController@retrieveReset');

    $router->match(['get', 'post'], 'oauth/login', 'AuthController@oauthLogin');
    $router->match(['get', 'post'], 'oauth/callback', 'AuthController@oauthCallback');
    $router->match(['get', 'post'], 'oauth/try_login', 'AuthController@oauthTryLogin');
    $router->match(['get', 'post'], 'oauth/bind', 'AuthController@oauthBind');
    $router->match(['get', 'post'], 'oauth/login_wechat_mini_program', 'AuthController@loginWechatMiniProgram');

    $router->match(['get', 'post'], 'sso/client_prepare', 'AuthController@ssoClientPrepare');
    $router->match(['get', 'post'], 'sso/client', 'AuthController@ssoClient');
    $router->match(['get', 'post'], 'sso/client_logout_prepare', 'AuthController@ssoClientLogoutPrepare');
    $router->match(['get', 'post'], 'sso/client_logout', 'AuthController@ssoClientLogout');
    $router->match(['get', 'post'], 'sso/server', 'AuthController@ssoServer');
    $router->match(['get', 'post'], 'sso/server_success', 'AuthController@ssoServerSuccess');
    $router->match(['get', 'post'], 'sso/server_logout', 'AuthController@ssoServerLogout');

    $router->match(['get', 'post'], 'member_profile/password', 'MemberProfileController@password');
    $router->match(['get', 'post'], 'member_profile/avatar', 'MemberProfileController@avatar');
    $router->match(['get', 'post'], 'member_profile/captcha', 'MemberProfileController@captcha');
    $router->match(['get', 'post'], 'member_profile/email', 'MemberProfileController@email');
    $router->match(['get', 'post'], 'member_profile/email_verify', 'MemberProfileController@emailVerify');
    $router->match(['get', 'post'], 'member_profile/phone', 'MemberProfileController@phone');
    $router->match(['get', 'post'], 'member_profile/phone_verify', 'MemberProfileController@phoneVerify');

    $router->match(['get', 'post'], 'member_message', 'MemberMessageController@paginate');
    $router->match(['get', 'post'], 'member_message/delete', 'MemberMessageController@delete');
    $router->match(['get', 'post'], 'member_message/read', 'MemberMessageController@read');
    $router->match(['get', 'post'], 'member_message/read_all', 'MemberMessageController@readAll');

    $router->match(['get', 'post'], 'member_vip/get', 'MemberVipController@get');
    $router->match(['get', 'post'], 'member_vip/all', 'MemberVipController@all');
    $router->match(['get', 'post'], 'member_vip/calc', 'MemberVipController@calc');
    $router->match(['get', 'post'], 'member_vip/buy', 'MemberVipController@buy');

    $router->match(['get', 'post'], 'member_data/file_manager/{category}', 'MemberDataController@fileManager');

    $router->match(['get', 'post'], 'member_money/cash/calc', 'MemberMoneyCashController@calc');
    $router->match(['get', 'post'], 'member_money/cash/submit', 'MemberMoneyCashController@submit');

    $router->match(['get', 'post'], 'member_favorite/favorite', 'MemberFavoriteController@favorite');
    $router->match(['get', 'post'], 'member_favorite/unfavorite', 'MemberFavoriteController@unfavorite');

});
