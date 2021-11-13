<?php


namespace Module\Member\Api\Controller;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mews\Captcha\Facades\Captcha;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Module\ModuleBaseController;
use Module\Member\Config\MemberOauth;
use Module\Member\Events\MemberUserLoginedEvent;
use Module\Member\Events\MemberUserPasswordResetedEvent;
use Module\Member\Events\MemberUserRegisteredEvent;
use Module\Member\Oauth\AbstractOauth;
use Module\Member\Provider\RegisterProcessor\AbstractMemberRegisterProcessorProvider;
use Module\Member\Provider\RegisterProcessor\MemberRegisterProcessorProvider;
use Module\Member\Util\MemberUtil;
use Module\Vendor\Email\MailSendJob;
use Module\Vendor\Provider\Captcha\CaptchaProvider;
use Module\Vendor\Session\SessionUtil;
use Module\Vendor\Sms\SmsUtil;
use Module\Vendor\Support\ResponseCodes;













class AuthController extends ModuleBaseController
{
    public function oauthTryLogin($oauthType = null)
    {
        $oauthUserInfo = Session::get('oauthUserInfo', []);
        if (empty($oauthUserInfo)) {
            return Response::generate(-1, '用户授权数据为空');
        }
        
        $oauth = MemberOauth::get($oauthType);
        $ret = $oauth->processTryLogin([
            'userInfo' => $oauthUserInfo,
        ]);
        BizException::throwsIfResponseError($ret);
        if ($ret['data']['memberUserId'] > 0) {
            Session::put('memberUserId', $ret['data']['memberUserId']);
            Session::forget('oauthUserInfo');
            return Response::generateSuccessData(['memberUserId' => $ret['data']['memberUserId']]);
        }
        return Response::generate(0, null, [
            'memberUserId' => 0,
        ]);
    }

    public function oauthBind($oauthType = null)
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', modstart_web_url(''));
        $oauthType = $input->getTrimString('type', $oauthType);
        $oauthUserInfo = Session::get('oauthUserInfo', []);
        if (empty($oauthUserInfo)) {
            return Response::generate(-1, '用户授权数据为空');
        }
        
        $oauth = MemberOauth::get($oauthType);
                $loginedMemberUserId = Session::get('memberUserId', 0);
        if ($loginedMemberUserId > 0) {
            $ret = $oauth->processBindToUser([
                'memberUserId' => $loginedMemberUserId,
                'userInfo' => $oauthUserInfo,
            ]);
            BizException::throwsIfResponseError($ret);
            Session::forget('oauthUserInfo');
            return Response::generate(0, null, null, $redirect);
        }
        $ret = $oauth->processTryLogin([
            'userInfo' => $oauthUserInfo,
        ]);
        BizException::throwsIfResponseError($ret);
        if ($ret['data']['memberUserId'] > 0) {
            Session::put('memberUserId', $ret['data']['memberUserId']);
            Session::forget('oauthUserInfo');
            return Response::generateSuccessData(['memberUserId' => $ret['data']['memberUserId']]);
        }
        if (modstart_config()->getWithEnv('registerDisable', false)) {
            return Response::generate(-1, '用户注册已禁用');
        }
        $username = $input->getTrimString('username');
        $ret = MemberUtil::register($username, null, null, null, true);
        if ($ret['code']) {
            return Response::generate(-1, $ret['msg']);
        }
        $memberUserId = $ret['data']['id'];
        $ret = $oauth->processBindToUser([
            'memberUserId' => $memberUserId,
            'userInfo' => $oauthUserInfo,
        ]);
        BizException::throwsIfResponseError($ret);
        Event::fire(new MemberUserRegisteredEvent($memberUserId));
        if (!empty($oauthUserInfo['avatar'])) {
            $avatarExt = FileUtil::extension($oauthUserInfo['avatar']);
            $avatar = CurlUtil::getRaw($oauthUserInfo['avatar']);
            if (!empty($avatar)) {
                if (empty($avatarExt)) {
                    $avatarExt = 'jpg';
                }
                MemberUtil::setAvatar($memberUserId, $avatar, $avatarExt);
            }
        }
        Session::put('memberUserId', $memberUserId);
        Session::forget('oauthUserInfo');
        return Response::generate(0, null);
    }

    public function oauthCallback($oauthType = null, $callback = null)
    {
        $input = InputPackage::buildFromInput();
        if (empty($oauthType)) {
            $oauthType = $input->getTrimString('type');
        }
        if (empty($callback)) {
            $callback = $input->getTrimString('callback', null);
        }
        $code = $input->getTrimString('code');
        if (empty($code)) {
            return Response::generate(-1, '登录失败(code为空)', null, '/');
        }
        
        $oauth = MemberOauth::get($oauthType);
        $ret = $oauth->processLogin([
            'code' => $code,
            'callback' => $callback,
        ]);
        BizException::throwsIfResponseError($ret);
        $userInfo = $ret['data']['userInfo'];
        $view = $input->getBoolean('view', false);
        if ($view) {
            Session::put('oauthViewOpenId_' . $oauthType, $userInfo['data']['openid']);
            return Response::generateSuccess();
        }
        Session::put('oauthUserInfo', $userInfo);
        return Response::generate(0, 'ok', [
            'user' => $userInfo,
        ]);
    }

    public function oauthLogin($oauthType = null, $callback = null)
    {
        $input = InputPackage::buildFromInput();
        if (empty($oauthType)) {
            $oauthType = $input->getTrimString('type');
        }
        if (empty($callback)) {
            $callback = $input->getTrimString('callback', 'NO_CALLBACK');
        }
        $silence = $input->getBoolean('silence', false);
        
        $oauth = MemberOauth::get($oauthType);
        $ret = $oauth->processRedirect([
            'callback' => $callback,
            'silence' => $silence,
        ]);
        BizException::throwsIfResponseError($ret);
        return Response::generate(0, 'ok', [
            'redirect' => $ret['data']['redirect'],
        ]);
    }

    public function ssoClientLogoutPrepare()
    {
        if (!modstart_config('ssoClientEnable', false)) {
            return Response::generate(-1, '请开启 同步登录客户端');
        }
        $input = InputPackage::buildFromInput();
        $domainUrl = $input->getTrimString('domainUrl');

        $ssoClientServer = modstart_config('ssoClientServer', '');
        if (empty($ssoClientServer)) {
            return Response::generate(-1, '请配置 同步登录服务端地址');
        }

        $redirect = $ssoClientServer . '_logout' . '?' . http_build_query(['redirect' => $domainUrl . '/sso/client_logout',]);
        return Response::generate(0, 'ok', [
            'redirect' => $redirect,
        ]);
    }

    public function ssoClientLogout()
    {
        if (!modstart_config('ssoClientEnable', false)) {
            return Response::generate(-1, '请开启 同步登录客户端');
        }
        Session::forget('memberUserId');
        return Response::generate(0, 'ok');
    }

    public function ssoServerLogout()
    {
        if (!modstart_config('ssoServerEnable', false)) {
            return Response::generate(-1, '请开启 同步登录服务端');
        }
        Session::forget('memberUserId');
        return Response::generate(0, 'ok');
    }

    public function ssoServerSuccess()
    {
        if (!modstart_config('ssoServerEnable', false)) {
            return Response::generate(-1, '请开启 同步登录服务端');
        }

        $memberUserId = Session::get('memberUserId', 0);
        if (!$memberUserId) {
            return Response::generate(-1, '未登录');
        }
        $memberUser = MemberUtil::get($memberUserId);
        $ssoServerSecret = modstart_config('ssoServerSecret');
        if (empty($ssoServerSecret)) {
            return Response::generate(-1, '请设置 同步登录服务端通讯秘钥');
        }

        $input = InputPackage::buildFromInput();
        $client = $input->getTrimString('client');
        $domainUrl = $input->getTrimString('domainUrl');
        if (empty($domainUrl) || empty($client)) {
            return Response::generate(-1, '数据错误');
        }
        $ssoClientList = explode("\n", modstart_config('ssoServerClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::generate(-1, '数据错误(2)');
        }
        $server = $domainUrl . '/sso/server';
        $timestamp = time();
        $username = $memberUser['username'];
        $sign = md5(md5($ssoServerSecret) . md5($timestamp . '') . md5($server) . md5($username));

        $redirect = $client
            . '?server=' . urlencode($server)
            . '&timestamp=' . $timestamp
            . '&username=' . urlencode(base64_encode($username))
            . '&sign=' . $sign;

        return Response::generate(0, null, [
            'redirect' => $redirect
        ]);
    }

    public function ssoServer()
    {
        if (!modstart_config('ssoServerEnable', false)) {
            return Response::generate(-1, '请开启 同步登录服务端');
        }
        $input = InputPackage::buildFromInput();
        $client = $input->getTrimString('client');
        $timestamp = $input->getInteger('timestamp');
        $sign = $input->getTrimString('sign');
        if (empty($client)) {
            return Response::generate(-1, 'client 为空');
        }
        if (empty($timestamp)) {
            return Response::generate(-1, 'timestamp 为空');
        }
        if (empty($sign)) {
            return Response::generate(-1, 'sign 为空');
        }
        $ssoSecret = modstart_config('ssoServerSecret');
        if (empty($ssoSecret)) {
            return Response::generate(-1, '请设置 同步登录服务端通讯秘钥');
        }
        $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));
        if ($sign != $signCalc) {
            return Response::generate(-1, 'sign 错误');
        }
        if (abs(time() - $timestamp) > 3600) {
            return Response::generate(-1, 'timestamp 错误');
        }
        $ssoClientList = explode("\n", modstart_config('ssoServerClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::generate(-1, '请在 同步登陆服务端增加客户端地址 ' . $client);
        }
        $isLogin = false;
        if (intval(Session::get('memberUserId', 0)) > 0) {
            $isLogin = true;
        }
        return Response::generate(0, 'ok', [
            'isLogin' => $isLogin,
        ]);
    }

    public function ssoClient()
    {
        if (!modstart_config('ssoClientEnable', false)) {
            return Response::generate(-1, '请开启 同步登录客户端');
        }
        $ssoClientServer = modstart_config('ssoClientServer', '');
        if (empty($ssoClientServer)) {
            return Response::generate(-1, '请配置 同步登录服务端地址');
        }

        $ssoClientSecret = modstart_config('ssoClientSecret');
        if (empty($ssoClientSecret)) {
            return Response::generate(-1, '请设置 同步登录客户端通讯秘钥');
        }

        $input = InputPackage::buildFromInput();
        $server = $input->getTrimString('server');
        $timestamp = $input->getInteger('timestamp');
        $sign = $input->getTrimString('sign');
        $username = @base64_decode($input->getTrimString('username'));

        if (empty($username)) {
            return Response::generate(-1, '同步登录返回的用户名为空');
        }
        if (empty($timestamp)) {
            return Response::generate(-1, 'timestamp为空');
        }
        if (empty($sign)) {
            return Response::generate(-1, 'sign为空');
        }
        $signCalc = md5(md5($ssoClientSecret) . md5($timestamp . '') . md5($server) . md5($username));
        if ($sign != $signCalc) {
            return Response::generate(-1, 'sign错误');
        }
        if (abs(time() - $timestamp) > 3600) {
            return Response::generate(-1, 'timestamp错误');
        }
        if ($server != $ssoClientServer) {
            return Response::generate(-1, '同步登录 服务端地址不是配置的' . $ssoClientServer);
        }
        $memberUser = MemberUtil::getByUsername($username);
        if (empty($memberUser)) {
            $ret = MemberUtil::register($username, null, null, null, true);
            if ($ret['code']) {
                return Response::generate(-1, $ret['msg']);
            }
            $memberUser = MemberUtil::get($ret['data']['id']);
        }
        Session::put('memberUserId', $memberUser['id']);
                return Response::generate(0, 'ok');
    }

    public function ssoClientPrepare()
    {
        if (!modstart_config('ssoClientEnable', false)) {
            return Response::generate(-1, 'SSO未开启');
        }
        $ssoClientServer = modstart_config('ssoClientServer');
        $ssoClientSecret = modstart_config('ssoClientSecret');
        $input = InputPackage::buildFromInput();
        $client = $input->getTrimString('client', '/');
        if (!Str::endsWith($client, '/sso/client')) {
            return Response::generate(-1, 'client参数错误');
        }
        $timestamp = time();
        $sign = md5(md5($ssoClientSecret) . md5($timestamp . '') . md5($client));
        $redirect = $ssoClientServer . '?client=' . urlencode($client) . '&timestamp=' . $timestamp . '&sign=' . $sign;
        return Response::generate(0, 'ok', [
            'redirect' => $redirect,
        ]);
    }

    public function logout()
    {
        Session::forget('memberUserId');
        return Response::generateSuccess();
    }

    public function login()
    {
        $input = InputPackage::buildFromInput();

        $username = $input->getTrimString('username');
        $password = $input->getTrimString('password');
        if (empty($username)) {
            return Response::generate(-1, '请输入用户');
        }
        if (empty($password)) {
            return Response::generate(-1, '请输入密码');
        }

        if (modstart_config('loginCaptchaEnable', false)) {
            $captchaProvider = modstart_config('loginCaptchaProvider', null);
            if ($captchaProvider) {
                $ret = CaptchaProvider::get($captchaProvider)->validate();
                if (Response::isError($ret)) {
                    return $ret;
                }
            } else {
                if (!Captcha::check($input->getTrimString('captcha'))) {
                    return Response::generate(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
                }
            }
        }
        $memberUser = null;
        if (!$memberUser) {
            $ret = MemberUtil::login($username, null, null, $password);
            if (0 == $ret['code']) {
                $memberUser = $ret['data'];
            }
        }
        if (!$memberUser) {
            $ret = MemberUtil::login(null, $username, null, $password);
            if (0 == $ret['code']) {
                $memberUser = $ret['data'];
            }
        }
        if (!$memberUser) {
            $ret = MemberUtil::login(null, null, $username, $password);
            if (0 == $ret['code']) {
                $memberUser = $ret['data'];
            }
        }
        if (!$memberUser) {
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '登录失败');
        }
        Session::put('memberUserId', $memberUser['id']);
        Event::fire(new MemberUserLoginedEvent($memberUser['id']));
        return Response::generateSuccess();
    }

    public function loginCaptchaRaw()
    {
        return Captcha::create('default');
    }

    public function loginCaptcha()
    {
        $captcha = $this->loginCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }

    public function register()
    {
        if (modstart_config('registerDisable', false)) {
            return Response::generate(-1, '禁止注册');
        }

        $input = InputPackage::buildFromInput();

        if (modstart_config('Member_AgreementEnable', false)) {
            if (!$input->getBoolean('agreement')) {
                return Response::generateError('请先同意 ' . modstart_config('Member_AgreementTitle', '用户使用协议'));
            }
        }

        $username = $input->getTrimString('username');
        $phone = $input->getPhone('phone');
        $phoneVerify = $input->getTrimString('phoneVerify');
        $email = $input->getEmail('email');
        $emailVerify = $input->getTrimString('emailVerify');
        $password = $input->getTrimString('password');
        $passwordRepeat = $input->getTrimString('passwordRepeat');
        $captcha = $input->getTrimString('captcha');

        if (empty($username)) {
            return Response::generate(-1, '用户名不能为空');
        }
        
        if (Str::contains($username, '@')) {
            return Response::generate(-1, '用户名不能包含特殊字符');
        }
        if (preg_match('/^\\d{11}$/', $username)) {
            return Response::generate(-1, '用户名不能为纯数字');
        }

        if (!Session::get('registerCaptchaPass', false)) {
            if (!Captcha::check($captcha)) {
                SessionUtil::atomicProduce('registerCaptchaPassCount', 1);
                return Response::generate(-1, '图片验证失败');
            }
        }
        if (!SessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::generate(-1, '请重新输入图片验证码');
        }

        if (modstart_config('registerPhoneEnable')) {
            if (empty($phone)) {
                return Response::generate(-1, '请输入手机');
            }
            if ($phoneVerify != Session::get('registerPhoneVerify')) {
                return Response::generate(-1, '手机验证码不正确.');
            }
            if (Session::get('registerPhoneVerifyTime') + 60 * 60 < time()) {
                return Response::generate(-1, '手机验证码已过期');
            }
            if ($phone != Session::get('registerPhone')) {
                return Response::generate(-1, '两次手机不一致');
            }
        }
        if (modstart_config('registerEmailEnable')) {
            if (empty($email)) {
                return Response::generate(-1, '请输入邮箱');
            }
            if ($emailVerify != Session::get('registerEmailVerify')) {
                return Response::generate(-1, '邮箱验证码不正确.');
            }
            if (Session::get('registerEmailVerifyTime') + 60 * 60 < time()) {
                return Response::generate(-1, '邮箱验证码已过期');
            }
            if ($email != Session::get('registerEmail')) {
                return Response::generate(-1, '两次邮箱不一致');
            }
        }
        if (empty($password)) {
            return Response::generate(-1, '请输入密码');
        }
        if ($password != $passwordRepeat) {
            return Response::generate(-1, '两次输入密码不一致');
        }

        foreach (MemberRegisterProcessorProvider::listAll() as $provider) {
            
            $ret = $provider->preCheck();
            if (Response::isError($ret)) {
                return $ret;
            }
        }

        $ret = MemberUtil::register($username, $phone, $email, $password);
        if ($ret['code']) {
            return Response::generate(-1, $ret['msg']);
        }
        $memberUserId = $ret['data']['id'];
        $update = [];
        if (modstart_config('registerPhoneEnable')) {
            $update['phoneVerified'] = true;
        }
        if (modstart_config('registerEmailEnable')) {
            $update['emailVerified'] = true;
        }
        if (!empty($update)) {
            MemberUtil::update($memberUserId, $update);
        }
        Event::fire(new MemberUserRegisteredEvent($memberUserId));
        Session::forget('registerCaptchaPass');
        foreach (MemberRegisterProcessorProvider::listAll() as $provider) {
            
            $provider->postProcess($memberUserId);
        }
        return Response::generate(0, '注册成功', [
            'id' => $memberUserId,
        ]);
    }

    public function registerEmailVerify()
    {
        if (modstart_config('registerDisable', false)) {
            return Response::generate(-1, '禁止注册');
        }
        if (!modstart_config('registerEmailEnable')) {
            return Response::generate(-1, '注册未开启邮箱');
        }
        $input = InputPackage::buildFromInput();

        $email = $input->getEmail('target');
        if (empty($email)) {
            return Response::generate(-1, '邮箱不能为空');
        }

        if (!Session::get('registerCaptchaPass', false)) {
            return Response::generate(-1, '请先验证图片验证码');
        }
        if (!SessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::generate(-1, '请重新输入图片验证码');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (!empty($memberUser)) {
            return Response::generate(-1, '邮箱已经被占用');
        }

        if (Session::get('registerEmailVerifyTime') && $email == Session::get('registerEmail')) {
            if (Session::get('registerEmailVerifyTime') + 60 > time()) {
                return Response::generate(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('registerEmailVerify', $verify);
        Session::put('registerEmailVerifyTime', time());
        Session::put('registerEmail', $email);

        MailSendJob::create($email, '注册账户验证码', 'verify', ['code' => $verify]);

        return Response::generate(0, '验证码发送成功');
    }

    public function registerPhoneVerify()
    {
        if (modstart_config('registerDisable', false)) {
            return Response::generate(-1, '禁止注册');
        }
        if (!modstart_config('registerPhoneEnable')) {
            return Response::generate(-1, '注册未开启手机');
        }
        $input = InputPackage::buildFromInput();

        $phone = $input->getPhone('target');
        if (empty($phone)) {
            return Response::generate(-1, '手机不能为空');
        }

        if (!Session::get('registerCaptchaPass', false)) {
            return Response::generate(-1, '请先验证图片验证码');
        }
        if (!SessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::generate(-1, '请重新输入图片验证码');
        }

        $memberUser = MemberUtil::getByPhone($phone);
        if (!empty($memberUser)) {
            return Response::generate(-1, '手机已经被占用');
        }

        if (Session::get('registerPhoneVerifyTime') && $phone == Session::get('registerPhone')) {
            if (Session::get('registerPhoneVerifyTime') + 60 > time()) {
                return Response::generate(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('registerPhoneVerify', $verify);
        Session::put('registerPhoneVerifyTime', time());
        Session::put('registerPhone', $phone);

        $ret = SmsUtil::send($phone, 'verify', ['code' => $verify]);

        return Response::generate(0, '验证码发送成功');
    }

    public function registerCaptchaVerify()
    {
        $input = InputPackage::buildFromInput();
        $captcha = $input->getTrimString('captcha');
        if (!Captcha::check($captcha)) {
            SessionUtil::atomicRemove('registerCaptchaPassCount');
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
        }
        Session::put('registerCaptchaPass', true);
        $registerCaptchaPassCount = 1;
        if (modstart_config('registerEmailEnable')) {
            $registerCaptchaPassCount++;
        }
        if (modstart_config('registerPhoneEnable')) {
            $registerCaptchaPassCount++;
        }
        SessionUtil::atomicProduce('registerCaptchaPassCount', $registerCaptchaPassCount);
        return Response::generateSuccess();
    }


    public function registerCaptchaRaw()
    {
        return Captcha::create('default');
    }

    public function registerCaptcha()
    {
        Session::forget('registerCaptchaPass');
        $captcha = $this->registerCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }

    public function retrievePhone()
    {
        if (modstart_config('retrieveDisable', false)) {
            return Response::generate(-1, '找回密码已禁用');
        }
        $input = InputPackage::buildFromInput();
        if (!modstart_config('retrievePhoneEnable', false)) {
            return Response::generate(-1, '找回密码没有开启');
        }
        $phone = $input->getPhone('phone');
        $verify = $input->getTrimString('verify');
        if (empty($phone)) {
            return Response::generate(-1, '手机为空或不正确');
        }
        if (empty($verify)) {
            return Response::generate(-1, '验证码不能为空');
        }
        if ($verify != Session::get('retrievePhoneVerify')) {
            return Response::generate(-1, '手机验证码不正确');
        }
        if (Session::get('retrievePhoneVerifyTime') + 60 * 60 < time()) {
            return Response::generate(0, '手机验证码已过期');
        }
        if ($phone != Session::get('retrievePhone')) {
            return Response::generate(-1, '两次手机不一致');
        }
        $memberUser = MemberUtil::getByPhone($phone);
        if (empty($memberUser)) {
            return Response::generate(-1, '手机没有绑定任何账号');
        }
        Session::forget('retrievePhoneVerify');
        Session::forget('retrievePhoneVerifyTime');
        Session::forget('retrievePhone');
        Session::put('retrieveMemberUserId', $memberUser['id']);
        return Response::generate(0, null);
    }

    public function retrievePhoneVerify()
    {
        if (modstart_config('retrieveDisable', false)) {
            return Response::generate(-1, '找回密码已禁用');
        }

        $input = InputPackage::buildFromInput();
        $phone = $input->getPhone('target');
        if (empty($phone)) {
            return Response::generate(-1, '手机为空或格式不正确');
        }

        $captcha = $input->getTrimString('captcha');
        if (!Captcha::check($captcha)) {
            return Response::generate(-1, '图片验证码错误');
        }

        $memberUser = MemberUtil::getByPhone($phone);
        if (empty($memberUser)) {
            return Response::generate(-1, '手机没有绑定任何账号');
        }

        if (Session::get('retrievePhoneVerifyTime') && $phone == Session::get('retrievePhone')) {
            if (Session::get('retrievePhoneVerifyTime') + 60 * 2 > time()) {
                return Response::generate(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('retrievePhoneVerify', $verify);
        Session::put('retrievePhoneVerifyTime', time());
        Session::put('retrievePhone', $phone);

        SmsUtil::send($phone, 'verify', ['code' => $verify]);

        return Response::generate(0, '验证码发送成功');
    }

    public function retrieveEmail()
    {
        if (modstart_config('retrieveDisable', false)) {
            return Response::generate(-1, '找回密码已禁用');
        }

        if (!modstart_config('retrieveEmailEnable', false)) {
            return Response::generate(-1, '找回密码没有开启');
        }

        $input = InputPackage::buildFromInput();

        $email = $input->getEmail('email');
        $verify = $input->getTrimString('verify');

        if (empty($email)) {
            return Response::generate(-1, '邮箱为空或格式不正确');
        }
        if (empty($verify)) {
            return Response::generate(-1, '验证码不能为空');
        }
        if ($verify != Session::get('retrieveEmailVerify')) {
            return Response::generate(-1, '邮箱验证码不正确');
        }
        if (Session::get('retrieveEmailVerifyTime') + 60 * 60 < time()) {
            return Response::generate(0, '邮箱验证码已过期');
        }
        if ($email != Session::get('retrieveEmail')) {
            return Response::generate(-1, '两次邮箱不一致');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (empty($memberUser)) {
            return Response::generate(-1, '邮箱没有绑定任何账号');
        }

        Session::forget('retrieveEmailVerify');
        Session::forget('retrieveEmailVerifyTime');
        Session::forget('retrieveEmail');

        Session::put('retrieveMemberUserId', $memberUser['id']);

        return Response::generate(0, null);
    }

    public function retrieveEmailVerify()
    {
        if (modstart_config('retrieveDisable', false)) {
            return Response::generate(-1, '找回密码已禁用');
        }

        $input = InputPackage::buildFromInput();

        $email = $input->getEmail('target');
        if (empty($email)) {
            return Response::generate(-1, '邮箱格式不正确或为空');
        }

        $captcha = $input->getTrimString('captcha');
        if (!Captcha::check($captcha)) {
            return Response::generate(-1, '图片验证码错误');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (empty($memberUser)) {
            return Response::generate(-1, '邮箱没有绑定任何账号');
        }

        if (Session::get('retrieveEmailVerifyTime') && $email == Session::get('retrieveEmail')) {
            if (Session::get('retrieveEmailVerifyTime') + 60 > time()) {
                return Response::generate(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('retrieveEmailVerify', $verify);
        Session::put('retrieveEmailVerifyTime', time());
        Session::put('retrieveEmail', $email);

        MailSendJob::create($email, '找回密码验证码', 'verify', ['code' => $verify]);

        return Response::generate(0, '验证码发送成功');
    }

    public function retrieveResetInfo()
    {
        $retrieveMemberUserId = Session::get('retrieveMemberUserId');
        if (empty($retrieveMemberUserId)) {
            return Response::generate(-1, '请求错误');
        }
        $memberUser = MemberUtil::get($retrieveMemberUserId);
        $username = $memberUser['username'];
        if (empty($username)) {
            $username = $memberUser['phone'];
        }
        if (empty($username)) {
            $username = $memberUser['email'];
        }
        return Response::generate(0, null, [
            'memberUser' => [
                'username' => $username,
            ]
        ]);
    }

    public function retrieveReset()
    {
        if (modstart_config('retrieveDisable', false)) {
            return Response::generate(-1, '找回密码已禁用');
        }

        $input = InputPackage::buildFromInput();
        $retrieveMemberUserId = Session::get('retrieveMemberUserId');
        if (empty($retrieveMemberUserId)) {
            return Response::generate(-1, '请求错误');
        }
        $password = $input->getTrimString('password');
        $passwordRepeat = $input->getTrimString('passwordRepeat');
        if (empty($password)) {
            return Response::generate(-1, '请输入密码');
        }
        if ($password != $passwordRepeat) {
            return Response::generate(-1, '两次输入密码不一致');
        }
        $memberUser = MemberUtil::get($retrieveMemberUserId);
        if (empty($memberUser)) {
            return Response::generate(-1, '用户不存在');
        }
        $ret = MemberUtil::changePassword($memberUser['id'], $password, null, true);
        if ($ret['code']) {
            return Response::generate(-1, $ret['msg']);
        }
        Event::fire(new MemberUserPasswordResetedEvent($memberUser['id'], $password));
        Session::forget('retrieveMemberUserId');
        return Response::generate(0, '成功设置新密码,请您登录');
    }

    public function retrieveCaptchaRaw()
    {
        return Captcha::create('default');
    }

    public function retrieveCaptcha()
    {
        $captcha = $this->retrieveCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }
}
