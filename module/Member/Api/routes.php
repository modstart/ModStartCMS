<?php
/* @var \Illuminate\Routing\Router $router */
$router->group([
    'middleware' => [
        \Module\Member\Middleware\ApiAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['post'], 'login', 'AuthController@login');
    $router->match(['post'], 'logout', 'AuthController@logout');
    $router->match(['post'], 'login_captcha', 'AuthController@loginCaptcha');
    $router->match(['post'], 'login_phone', 'AuthController@loginPhone');
    $router->match(['post'], 'login_phone_verify', 'AuthController@loginPhoneVerify');
    $router->match(['post'], 'login_phone_captcha', 'AuthController@loginPhoneCaptcha');
    $router->match(['post'], 'register', 'AuthController@register');
    $router->match(['post'], 'register_email_verify', 'AuthController@registerEmailVerify');
    $router->match(['post'], 'register_phone', 'AuthController@registerPhone');
    $router->match(['post'], 'register_phone_verify', 'AuthController@registerPhoneVerify');
    $router->match(['post'], 'register_captcha', 'AuthController@registerCaptcha');
    $router->match(['post'], 'register_captcha_verify', 'AuthController@registerCaptchaVerify');
    $router->match(['post'], 'retrieve_phone', 'AuthController@retrievePhone');
    $router->match(['post'], 'retrieve_phone_verify', 'AuthController@retrievePhoneVerify');
    $router->match(['post'], 'retrieve_email', 'AuthController@retrieveEmail');
    $router->match(['post'], 'retrieve_email_verify', 'AuthController@retrieveEmailVerify');
    $router->match(['post'], 'retrieve_captcha', 'AuthController@retrieveCaptcha');
    $router->match(['post'], 'retrieve_reset_info', 'AuthController@retrieveResetInfo');
    $router->match(['post'], 'retrieve_reset', 'AuthController@retrieveReset');

    $router->match(['post'], 'oauth/login', 'AuthController@oauthLogin');
    $router->match(['post'], 'oauth/callback', 'AuthController@oauthCallback');
    $router->match(['post'], 'oauth/try_login', 'AuthController@oauthTryLogin');
    $router->match(['post'], 'oauth/bind_info', 'AuthController@oauthBindInfo');
    $router->match(['post'], 'oauth/bind', 'AuthController@oauthBind');
    $router->match(['post'], 'oauth/bind_captcha', 'AuthController@oauthBindCaptcha');
    $router->match(['post'], 'oauth/bind_captcha_verify', 'AuthController@oauthBindCaptchaVerify');
    $router->match(['post'], 'oauth/bind_phone_verify', 'AuthController@oauthBindPhoneVerify');
    $router->match(['post'], 'oauth/bind_email_verify', 'AuthController@oauthBindEmailVerify');

    $router->match(['post'], 'sso/client_prepare', 'AuthController@ssoClientPrepare');
    $router->match(['post'], 'sso/client', 'AuthController@ssoClient');
    $router->match(['post'], 'sso/client_logout_prepare', 'AuthController@ssoClientLogoutPrepare');
    $router->match(['post'], 'sso/client_logout', 'AuthController@ssoClientLogout');
    $router->match(['post'], 'sso/server', 'AuthController@ssoServer');
    $router->match(['post'], 'sso/server_success', 'AuthController@ssoServerSuccess');
    $router->match(['post'], 'sso/server_logout', 'AuthController@ssoServerLogout');

    $router->match(['post'], 'member_profile/password', 'MemberProfileController@password');
    $router->match(['post'], 'member_profile/avatar', 'MemberProfileController@avatar');
    $router->match(['post'], 'member_profile/captcha', 'MemberProfileController@captcha');
    $router->match(['post'], 'member_profile/email', 'MemberProfileController@email');
    $router->match(['post'], 'member_profile/email_verify', 'MemberProfileController@emailVerify');
    $router->match(['post'], 'member_profile/phone', 'MemberProfileController@phone');
    $router->match(['post'], 'member_profile/phone_verify', 'MemberProfileController@phoneVerify');
    $router->match(['post'], 'member_profile/oauth_unbind', 'MemberProfileController@oauthUnbind');
    $router->match(['post'], 'member_profile/delete_info', 'MemberProfileController@deleteInfo');
    $router->match(['post'], 'member_profile/delete', 'MemberProfileController@delete');
    $router->match(['post'], 'member_profile/delete_revert', 'MemberProfileController@deleteRevert');
    $router->match(['post'], 'member_profile/nickname', 'MemberProfileController@nickname');

    $router->match(['post'], 'member_message', 'MemberMessageController@paginate');
    $router->match(['post'], 'member_message/delete', 'MemberMessageController@delete');
    $router->match(['post'], 'member_message/read', 'MemberMessageController@read');
    $router->match(['post'], 'member_message/read_all', 'MemberMessageController@readAll');
    $router->match(['post'], 'member_message/delete_all', 'MemberMessageController@deleteAll');

    $router->match(['post'], 'member_vip/get', 'MemberVipController@get');
    $router->match(['post'], 'member_vip/info', 'MemberVipController@info');
    $router->match(['post'], 'member_vip/all', 'MemberVipController@all');
    $router->match(['post'], 'member_vip/calc', 'MemberVipController@calc');
    $router->match(['post'], 'member_vip/buy', 'MemberVipController@buy');

    $router->match(['post'], 'member_address/all', 'MemberAddressController@all');
    $router->match(['post'], 'member_address/get_default', 'MemberAddressController@getDefault');
    $router->match(['post'], 'member_address/edit', 'MemberAddressController@edit');
    $router->match(['post'], 'member_address/delete', 'MemberAddressController@delete');

    $router->match(['post'], 'member_data/file_manager/{category}', 'MemberDataController@fileManager');

    $router->match(['post'], 'member_money/get', 'MemberMoneyController@get');
    $router->match(['post'], 'member_money/log', 'MemberMoneyController@log');
    $router->match(['post'], 'member_money/charge/submit', 'MemberMoneyChargeController@submit');
    $router->match(['post'], 'member_money/cash/get', 'MemberMoneyCashController@get');
    $router->match(['post'], 'member_money/cash/calc', 'MemberMoneyCashController@calc');
    $router->match(['post'], 'member_money/cash/submit', 'MemberMoneyCashController@submit');
    $router->match(['post'], 'member_money/cash/log', 'MemberMoneyCashController@log');

    $router->match(['post'], 'member_credit/get', 'MemberCreditController@get');
    $router->match(['post'], 'member_credit/log', 'MemberCreditController@log');

    $router->match(['post'], 'member_favorite/favorite', 'MemberFavoriteController@favorite');
    $router->match(['post'], 'member_favorite/unfavorite', 'MemberFavoriteController@unfavorite');

    $router->match(['post'], 'member_doc/get', 'MemberDocController@get');

});
