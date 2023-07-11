<?php


namespace Module\Member\Api\Controller;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\EventUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Misc\Captcha\CaptchaFacade;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Config\MemberOauth;
use Module\Member\Events\MemberUserLoginedEvent;
use Module\Member\Events\MemberUserLogoutEvent;
use Module\Member\Events\MemberUserPasswordResetedEvent;
use Module\Member\Events\MemberUserRegisteredEvent;
use Module\Member\Oauth\AbstractOauth;
use Module\Member\Provider\RegisterProcessor\AbstractMemberRegisterProcessorProvider;
use Module\Member\Provider\RegisterProcessor\MemberRegisterProcessorProvider;
use Module\Member\Util\MemberUtil;
use Module\Member\Util\SecurityUtil;
use Module\Vendor\Job\MailSendJob;
use Module\Vendor\Job\SmsSendJob;
use Module\Vendor\Support\ResponseCodes;
use Module\Vendor\Util\SessionUtil;

/**
 * 相关配置开关
 *
 * - loginCaptchaEnable
 * - registerDisable
 * - registerEmailEnable
 * - registerPhoneEnable
 * - retrieveDisable
 * - retrievePhoneEnable
 * - retrieveEmailEnable
 * - ssoServerEnable
 * - ssoServerSecret
 * - ssoServerClientList
 * - ssoClientEnable
 * - ssoClientSecret
 * - ssoClientServer
 */

/**
 * ############### 系列产品 SSO Client 登录流程 ###############
 *
 * 1. Client 访问 http://client.com/login 页面，前台检测到SSO登录是否已开启 (config.ssoClientEnable) ；
 * 2. Client 请求 http://client.com/api/sso/client_prepare ，拿到需要跳转的SSO登录地址，SSO登录地址包含如下参数：
 *              client      -> http://client.com/sso/client
 *              timestamp   -> time()
 *              sign        -> md5(md5(ssoClientSecret) + md5(timestamp) + md5(client))
 * 3. Client 记录当前登录 redirect 存储到 Storage（ssoClientRedirect），同时跳转到SSO登录地址；
 * 4. Server 端授权登录跳回页面 http://client.com/sso/client （第2步传递的client） 并附带参数
 *              server      -> http://server.com/sso/server
 *              timestamp   -> time()
 *              username    -> base64_encode(username)
 *              sign        -> md5( md5(ssoServerSecret) + md5(timestamp) + md5(server) + md5(username) )
 * 5. Client 访问到 http://client.com/sso/client 请求 http://client.com/api/sso/client 并验证参数：
 *              验证 sign 是否正确；
 *              验证 timestamp 是否合法,误差不能相差 1 小时；
 *              验证 server 是否为预期 (config.ssoClientServer)；
 * 6. Client 验证完全正确后，根据 username 来进行登录,如果用户不存在创建用户,如果用户已经存在直接设置为已登录状态;
 * 7. Client 前端跳转到 ssoClientRedirect（从Storage读取）；
 */


/**
 * ############### 系列产品 SSO Server 登录流程 ###############
 *
 * 1. Server 访问 http://server.com/sso/server 页面 （跳转过来的登录请求），附带以下参数;
 *              client      -> http://client.com/sso/client
 *              timestamp   -> time()
 *              sign        -> md5( md5(ssoServerSecret) + md5(timestamp) + md5(client) )
 * 2. Server 发送第1步所有参数到，记录 ssoServerClient 到 Storage；
 * 3. Server 请求 http://server.com/api/sso/server ，同时返回 isLogin 参数，验证以下信息：
 *              检测 SSO 登录是否开启 (config.ssoServerEnable);
 *              sign        -> 验证 sign 是否正确;
 *              验证 timestamp 是否合法,误差不能超过 1 小时;
 *              验证 client 是否为预期 (config.ssoServerClientList);
 * 4. Server 如果判断 isLogin 为真, 直接跳转到 http://server.com/sso/server_success;
 * 5. Server 如果判断 isLogin 为假，跳转到登录页面 /login 并附带以下参数:
 *              redirect    -> http://server.com/sso/server_success
 * 6. Server 用户登录，登录成功后重定向到 http://server.com/sso/server_success ;
 * 4. Server 请求 http://server.com/api/sso/server_success 接口，
 *    携带以下参数
 *              client      -> http://client.com/sso/client （从 Storage 读取 ssoServerClient）
 *              domainUrl   -> http://server.com
 *    获取到以下参数
 *              redirect    -> http://client.com/sso/client?server=xxx&timestamp=xxx&username=xxx&sign=xxx
 *                             server      -> http://server.com/sso/server
 *                             timestamp   -> time()
 *                             username    -> base64_encode(username)
 *                             sign        -> md5( md5(ssoServerSecret) + md5(timestamp) + md5(server) + md5(username) )
 * 7. Server 页面跳转到 redirect
 */

/**
 * ############### 系列产品 SSO Client 退出流程 ###############
 * 1. Client 需要退出登陆,跳转到 http://client.com/logout 并附带以下参数：
 *              redirect    -> <logout-to-go> 地址
 * 2. Client 判断如果检测到SSO登录开启（config.ssoClientEnable），记录 redirect 到 Storage 为 ssoLogoutRedirect；
 * 2. Client 请求 http://client.com/api/sso/client_logout_prepare ，返回以下参数：
 *              redirect    -> http://server.com/sso/server_logout?redirect=urlencode(http://client.com/sso/client_logout)
 * 3. Client 跳转到 redirect
 * 4. Server 返回到 http://client.com/sso/client_logout ，请求 http://client.com/api/sso/client_logout ；
 * 5. Client 从 Storage 读取 ssoLogoutRedirect ，跳转到 ssoLogout ；
 */

/**
 * ############### 系列产品 SSO Server 退出流程 ###############
 * 1. Client 页面访问 http://server.com/sso/server_logout 并附带以下参数：
 *              redirect    -> <logout-to-go>
 * 2. Client 请求 http://server.com/api/sso/server_logout ，返回以下参数：
 *              redirect    -> <logout-to-go>
 * 3. Client 跳转到 redirect
 */

/**
 * Class AuthController
 * @package Module\Member\Api\Controller
 * @Api 用户授权
 */
class AuthController extends ModuleBaseController
{
    public function checkRedirectSafety($redirect)
    {
        if (!modstart_config('Member_LoginRedirectCheckEnable', false)) {
            return;
        }
        $info = parse_url($redirect);
        // ignore local url
        if (empty($info['host'])) {
            return;
        }
        if ($info['host'] == Request::domain()) {
            return;
        }
        $whiteList = modstart_config('Member_LoginRedirectWhiteList', '');
        $whiteList = explode("\n", $whiteList);
        $whiteList = array_filter($whiteList);
        foreach ($whiteList as $item) {
            if ($info['host'] == $item) {
                return;
            }
        }
        BizException::throws("登录跳转路径异常");
    }

    public function oauthTryLogin($oauthType = null)
    {
        $oauthUserInfo = Session::get('oauthUserInfo', []);
        if (empty($oauthUserInfo)) {
            return Response::generate(-1, '用户授权数据为空');
        }
        if (empty($oauthType)) {
            $input = InputPackage::buildFromInput();
            $oauthType = $input->getTrimString('type');
        }
        BizException::throwsIfEmpty('授权类型为空', $oauthType);
        /** @var AbstractOauth $oauth */
        $oauth = MemberOauth::getOrFail($oauthType);
        $ret = $oauth->processTryLogin([
            'userInfo' => $oauthUserInfo,
        ]);
        BizException::throwsIfResponseError($ret);
        if ($ret['data']['memberUserId'] > 0) {
            Session::put('memberUserId', $ret['data']['memberUserId']);
            MemberUtil::fireLogin($ret['data']['memberUserId']);
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
        $redirect = $input->getTrimString('redirect', modstart_web_url('member'));
        $oauthType = $input->getTrimString('type', $oauthType);
        $oauthUserInfo = Session::get('oauthUserInfo', []);
        if (empty($oauthUserInfo)) {
            return Response::generate(-1, '用户授权数据为空');
        }
        /** @var AbstractOauth $oauth */
        $oauth = MemberOauth::getOrFail($oauthType);
        //如果用户已经登录直接关联到当前用户
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
            MemberUtil::fireLogin($ret['data']['memberUserId']);
            Session::forget('oauthUserInfo');
            return Response::generateSuccessData(['memberUserId' => $ret['data']['memberUserId']]);
        }
        if (modstart_config()->getWithEnv('registerDisable', false)
            && !modstart_config()->getWithEnv('registerOauthEnable', false)) {
            return Response::generate(-1, '用户注册已禁用');
        }
        $username = $input->getTrimString('username');
        /** 为了兼容统一登录，禁止使用手机号格式和邮箱格式  */
        if (Str::contains($username, '@')) {
            return Response::generate(-1, '用户名不能包含特殊字符');
        }
        if (preg_match('/^\\d{11}$/', $username)) {
            return Response::generate(-1, '用户名不能为纯数字');
        }

        $phone = $input->getPhone('phone');
        $phoneVerify = $input->getTrimString('phoneVerify');
        $email = $input->getEmail('email');
        $emailVerify = $input->getTrimString('emailVerify');
        $captcha = $input->getTrimString('captcha');

        if (!Session::get('oauthBindCaptchaPass', false)) {
            if (!CaptchaFacade::check($captcha)) {
                SessionUtil::atomicProduce('oauthBindCaptchaPassCount', 1);
                return Response::generate(-1, '请重新进行安全验证');
            }
        }
        if (!SessionUtil::atomicConsume('oauthBindCaptchaPassCount')) {
            return Response::generate(-1, '请进行安全验证');
        }
        if (modstart_config('Member_OauthBindPhoneEnable')) {
            if (empty($phone)) {
                return Response::generate(-1, '请输入手机');
            }
            $phoneVerifyCheck = Session::get('oauthBindPhoneVerify');
            if ($phoneVerify != $phoneVerifyCheck) {
                Log::info('Member.OauthBind.PhoneVerifyError - ' . $phoneVerify . ' - ' . $phoneVerifyCheck);
                return Response::generate(-1, '手机验证码不正确.');
            }
            if (Session::get('oauthBindPhoneVerifyTime') + 60 * 60 < time()) {
                return Response::generate(-1, '手机验证码已过期');
            }
            if ($phone != Session::get('oauthBindPhone')) {
                return Response::generate(-1, '两次手机不一致');
            }
        }
        if (modstart_config('Member_OauthBindEmailEnable')) {
            if (empty($email)) {
                return Response::generate(-1, '请输入邮箱');
            }
            $emailVerifyCheck = Session::get('oauthBindEmailVerify');
            if ($emailVerify != $emailVerifyCheck) {
                Log::info('Member.OauthBind.EmailVerifyError - ' . $emailVerify . ' - ' . $emailVerifyCheck);
                return Response::generate(-1, '邮箱验证码不正确.');
            }
            if (Session::get('oauthBindEmailVerifyTime') + 60 * 60 < time()) {
                return Response::generate(-1, '邮箱验证码已过期');
            }
            if ($email != Session::get('oauthBindEmail')) {
                return Response::generate(-1, '两次邮箱不一致');
            }
        }

        $ret = MemberUtil::register($username, $phone, $email, null, true);
        if ($ret['code']) {
            return Response::generate(-1, $ret['msg']);
        }
        $memberUserId = $ret['data']['id'];
        $update = [];
        if (modstart_config('Member_OauthBindPhoneEnable')) {
            $update['phoneVerified'] = true;
        }
        if (modstart_config('Member_OauthBindEmailEnable')) {
            $update['emailVerified'] = true;
        }
        $update['registerIp'] = StrUtil::mbLimit(Request::ip(), 20);
        if (!empty($update)) {
            MemberUtil::update($memberUserId, $update);
        }
        $ret = $oauth->processBindToUser([
            'memberUserId' => $memberUserId,
            'userInfo' => $oauthUserInfo,
        ]);
        BizException::throwsIfResponseError($ret);
        EventUtil::fire(new MemberUserRegisteredEvent($memberUserId));
        if (!empty($oauthUserInfo['avatar'])) {
            $avatarExt = FileUtil::extension($oauthUserInfo['avatar']);
            $allowImageExts = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($avatarExt, $allowImageExts)) {
                Log::info('Member.Auth.OauthBind.AvatarExtError - ' . $avatarExt . ' - ' . $oauthUserInfo['avatar']);
                $avatarExt = null;
            }
            $avatarRet = CurlUtil::get($oauthUserInfo['avatar'], [], [
                'returnHeader' => true,
            ]);
            if (!empty($avatarRet['body'])) {
                if (empty($avatarExt) && !empty($ret['headerMap']['content-type'])) {
                    $avatarExt = FileUtil::mimeToExt($ret['headerMap']['content-type']);
                    if (!in_array($avatarExt, $allowImageExts)) {
                        Log::info('Member.Auth.OauthBind.AvatarExtGuessError - ' . $avatarExt . ' - ' . $oauthUserInfo['avatar']);
                        $avatarExt = null;
                    }
                }
                if (!empty($avatarExt)) {
                    MemberUtil::setAvatar($memberUserId, $avatarRet['body'], $avatarExt);
                }
            }
        }
        Session::put('memberUserId', $memberUserId);
        MemberUtil::fireLogin($memberUserId);
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
            $code = $input->getTrimString('auth_code');
        }
        if (empty($code)) {
            return Response::generate(-1, '登录失败(code为空)', null, '/');
        }
        /** @var AbstractOauth $oauth */
        $oauth = MemberOauth::getOrFail($oauthType);
        $param = Session::get('oauthLoginParam', []);
        Session::forget('oauthLoginParam');
        $ret = $oauth->processLogin(array_merge($param, [
            'code' => $code,
            'callback' => $callback,
        ]));
        if (!isset($ret['code'])) {
            return Response::generate(-1, '登录失败(返回结果为空)');
        }
        if (0 != $ret['code']) {
            return $ret;
        }
        $userInfo = $ret['data']['userInfo'];
        $view = $input->getBoolean('view', false);
        if ($view) {
            Session::put('oauthViewOpenId_' . $oauthType, $userInfo['openid']);
            return Response::generateSuccess();
        }
        Session::put('oauthUserInfo', $userInfo);
        return Response::generate(0, 'ok', [
            'user' => $userInfo,
        ]);
    }

    public function oauthLogin($oauthType = null, $callback = null)
    {
        if ($disableText = modstart_config()->getWithEnv('oauthDisableText')) {
            return Response::generateError($disableText);
        }
        $input = InputPackage::buildFromInput();
        if (empty($oauthType)) {
            $oauthType = $input->getTrimString('type');
        }
        if (empty($callback)) {
            $callback = $input->getTrimString('callback', 'NO_CALLBACK');
        }
        $silence = $input->getBoolean('silence', false);
        /** @var AbstractOauth $oauth */
        $oauth = MemberOauth::getOrFail($oauthType);
        $param = [
            'callback' => $callback,
            'silence' => $silence,
        ];
        Session::put('oauthLoginParam', $param);
        $ret = $oauth->processRedirect($param);
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
        MemberUtil::fireLogin($memberUser['id']);
        // return Response::generateError('forbidden');
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

    /**
     * @return array
     *
     * @Api 登录-退出登录
     */
    public function logout()
    {
        $memberUserId = MemberUser::id();
        Session::forget('memberUserId');
        if ($memberUserId > 0) {
            EventUtil::fire(new MemberUserLogoutEvent($memberUserId));
        }
        return Response::generateSuccess();
    }

    /**
     * @return array
     * @throws BizException
     *
     * @Api 登录-用户登录
     * @ApiBodyParam username string required 用户名
     * @ApiBodyParam password string required 密码
     * @ApiBodyParam captcha string 验证码（如果验证码开启需要传递）
     */
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
            $captchaProvider = SecurityUtil::loginCaptchaProvider();
            if ($captchaProvider) {
                $ret = $captchaProvider->validate();
                if (Response::isError($ret)) {
                    return Response::generate(-1, $ret['msg']);
                }
            } else {
                if (!CaptchaFacade::check($input->getTrimString('captcha'))) {
                    return Response::generate(ResponseCodes::CAPTCHA_ERROR, '登录失败:图片验证码错误', null, '[js]$(\'[data-captcha]\').click();');
                }
            }
        }
        $memberUser = null;
        $loginMsg = null;
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
            $failedTip = Session::pull('memberUserLoginFailedTip', null);
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '登录失败:用户或密码错误' . ($failedTip ? '，' . $failedTip : ''));
        }
        Session::put('memberUserId', $memberUser['id']);
        MemberUtil::fireLogin($memberUser['id']);
        EventUtil::fire(new MemberUserLoginedEvent($memberUser['id']));
        return Response::generateSuccess();
    }

    public function loginCaptchaRaw()
    {
        return CaptchaFacade::create('default');
    }

    public function loginPhoneCaptchaRaw()
    {
        return CaptchaFacade::create('default');
    }

    /**
     * @Api 手机快捷登录-登录提交
     * @ApiBodyParam phone string 手机号
     * @ApiBodyParam verify string 手机验证码
     */
    public function loginPhone()
    {
        if (!modstart_config('Member_LoginPhoneEnable', false)) {
            return Response::generate(-1, '手机快捷登录未开启');
        }
        $input = InputPackage::buildFromInput();
        $phone = $input->getPhone('phone');
        $verify = $input->getTrimString('verify');
        if (empty($phone)) {
            return Response::generate(-1, '手机为空或不正确');
        }
        if (empty($verify)) {
            return Response::generate(-1, '验证码不能为空');
        }
        $verifyCheck = Session::get('loginPhoneVerify');
        if ($verify != $verifyCheck) {
            Log::info('Member.LoginPhone.LoginVerifyError - ' . $verify . ' - ' . $verifyCheck);
            return Response::generate(-1, '手机验证码不正确');
        }
        if (Session::get('loginPhoneVerifyTime') + 60 * 60 < time()) {
            return Response::generate(0, '手机验证码已过期');
        }
        if ($phone != Session::get('loginPhone')) {
            return Response::generate(-1, '两次手机不一致');
        }
        $memberUser = MemberUtil::getByPhone($phone);
        // 自动注册
        if (empty($memberUser) && modstart_config('Member_LoginPhoneAutoRegister', false)) {
            foreach (MemberRegisterProcessorProvider::listAll() as $provider) {
                /** @var AbstractMemberRegisterProcessorProvider $provider */
                $ret = $provider->preCheck();
                if (Response::isError($ret)) {
                    return $ret;
                }
            }
            $ret = MemberUtil::register(null, $phone, null, null, true);
            if ($ret['code']) {
                return Response::generate(-1, $ret['msg']);
            }
            $memberUserId = $ret['data']['id'];
            MemberUtil::autoSetUsernameNickname($memberUserId, modstart_config('Member_LoginPhoneNameSuggest', '用户'));
            $update = [];
            $update['phoneVerified'] = true;
            $update['registerIp'] = StrUtil::mbLimit(Request::ip(), 20);
            if (!empty($update)) {
                MemberUtil::update($memberUserId, $update);
            }
            EventUtil::fire(new MemberUserRegisteredEvent($memberUserId));
            Session::forget('registerCaptchaPass');
            foreach (MemberRegisterProcessorProvider::listAll() as $provider) {
                /** @var AbstractMemberRegisterProcessorProvider $provider */
                $provider->postProcess($memberUserId);
            }
            $memberUser = MemberUtil::get($memberUserId);
        }
        if (empty($memberUser)) {
            return Response::generate(-1, '手机没有绑定任何账号');
        }
        Session::forget('loginPhoneVerify');
        Session::forget('loginPhoneVerifyTime');
        Session::forget('loginPhone');
        Session::put('memberUserId', $memberUser['id']);
        MemberUtil::fireLogin($memberUser['id']);
        EventUtil::fire(new MemberUserLoginedEvent($memberUser));
        return Response::generate(0, null);
    }

    /**
     * @return array
     * @Api 手机快捷登录-获取手机验证码
     * @ApiBodyParam target string 手机号
     * @ApiBodyParam captcha string 图片验证码
     */
    public function loginPhoneVerify()
    {
        if (!modstart_config('Member_LoginPhoneEnable', false)) {
            return Response::generate(-1, '手机快捷登录未开启');
        }

        $input = InputPackage::buildFromInput();
        $phone = $input->getPhone('target');
        if (empty($phone)) {
            return Response::generate(-1, '手机为空或格式不正确');
        }

        $provider = SecurityUtil::loginCaptchaProvider();
        if ($provider) {
            $ret = $provider->validate();
            if (Response::isError($ret)) {
                return $ret;
            }
        } else {
            $captcha = $input->getTrimString('captcha');
            if (!CaptchaFacade::check($captcha)) {
                return Response::generate(-1, '图片验证码错误');
            }
        }

        $memberUser = MemberUtil::getByPhone($phone);
        if (empty($memberUser) && !modstart_config('Member_LoginPhoneAutoRegister', false)) {
            return Response::generate(-1, '手机没有绑定任何账号');
        }

        if (Session::get('loginPhoneVerifyTime') && $phone == Session::get('loginPhone')) {
            if (Session::get('loginPhoneVerifyTime') + 60 > time()) {
                return Response::generate(-1, '验证码发送频繁，请稍后再试!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('loginPhoneVerify', $verify);
        Session::put('loginPhoneVerifyTime', time());
        Session::put('loginPhone', $phone);

        SmsSendJob::create($phone, 'verify', ['code' => $verify]);

        return Response::generate(0, '验证码发送成功');
    }

    /**
     * @return array
     *
     * @Api 手机快捷登录-图片验证码
     * @ApiResponseData {
     *   "image":"图片Base64"
     * }
     */
    public function loginPhoneCaptcha()
    {
        $captcha = $this->loginCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }

    /**
     * @return array
     *
     * @Api 登录-图片验证码
     * @ApiResponseData {
     *   "image":"图片Base64"
     * }
     */
    public function loginCaptcha()
    {
        $captcha = $this->loginCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }

    /**
     * @Api 注册-手机快速注册
     * @ApiBodyParam phone string 手机号
     * @ApiBodyParam phoneVerify string 手机验证码
     * @ApiBodyParam agreement boolean 是否同意协议
     */
    public function registerPhone()
    {
        if (modstart_config('registerDisable', false)) {
            return Response::generate(-1, '禁止注册');
        }
        if (!modstart_config('Member_RegisterPhoneEnable', false)) {
            return Response::generate(-1, '手机快速注册未开启');
        }
        $input = InputPackage::buildFromInput();
        if (modstart_config('Member_AgreementEnable', false)) {
            if (!$input->getBoolean('agreement')) {
                return Response::generateError('请先同意 ' . modstart_config('Member_AgreementTitle', '用户使用协议'));
            }
        }
        $phone = $input->getPhone('phone');
        $phoneVerify = $input->getTrimString('phoneVerify');

        if (empty($phone)) {
            return Response::generate(-1, '请输入手机');
        }
        $phoneVerifyCheck = Session::get('registerPhoneVerify');
        if ($phoneVerify != $phoneVerifyCheck) {
            Log::info('Member.RegisterPhone.PhoneVerifyError - ' . $phoneVerify . ' - ' . $phoneVerifyCheck);
            return Response::generate(-1, '手机验证码不正确.');
        }
        if (Session::get('registerPhoneVerifyTime') + 60 * 60 < time()) {
            return Response::generate(-1, '手机验证码已过期');
        }
        if ($phone != Session::get('registerPhone')) {
            return Response::generate(-1, '两次手机不一致');
        }

        foreach (MemberRegisterProcessorProvider::listAll() as $provider) {
            /** @var AbstractMemberRegisterProcessorProvider $provider */
            $ret = $provider->preCheck();
            if (Response::isError($ret)) {
                return $ret;
            }
        }

        $ret = MemberUtil::register(null, $phone, null, null, true);
        if ($ret['code']) {
            return Response::generate(-1, $ret['msg']);
        }
        $memberUserId = $ret['data']['id'];
        MemberUtil::autoSetUsernameNickname($memberUserId, modstart_config('Member_LoginPhoneNameSuggest', '用户'));
        $update = [];
        $update['phoneVerified'] = true;
        $update['registerIp'] = StrUtil::mbLimit(Request::ip(), 20);
        if (!empty($update)) {
            MemberUtil::update($memberUserId, $update);
        }
        EventUtil::fire(new MemberUserRegisteredEvent($memberUserId));
        Session::forget('registerCaptchaPass');
        foreach (MemberRegisterProcessorProvider::listAll() as $provider) {
            /** @var AbstractMemberRegisterProcessorProvider $provider */
            $provider->postProcess($memberUserId);
        }
        Session::put('memberUserId', $memberUserId);
        MemberUtil::fireLogin($memberUserId);
        EventUtil::fire(new MemberUserLoginedEvent($memberUserId));
        return Response::generate(0, '注册成功', [
            'id' => $memberUserId,
        ]);
    }

    /**
     * @Api 注册-用户注册
     * @ApiBodyParam username string 用户名
     * @ApiBodyParam password string 密码
     * @ApiBodyParam passwordRepeat string 重复密码
     * @ApiBodyParam phone string 手机号（如果开启手机注册需要传递）
     * @ApiBodyParam phoneVerify string 手机验证码（如果开启手机注册需要传递）
     * @ApiBodyParam email string 邮箱（如果开启邮箱注册需要传递）
     * @ApiBodyParam emailVerify string 邮箱验证码（如果开启邮箱注册需要传递）
     * @ApiBodyParam captcha string 验证码
     * @ApiBodyParam agreement boolean 是否同意协议
     */
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
        /** 为了兼容统一登录，禁止使用手机号格式和邮箱格式  */
        if (Str::contains($username, '@')) {
            return Response::generate(-1, '用户名不能包含特殊字符');
        }
        if (preg_match('/^\\d{11}$/', $username)) {
            return Response::generate(-1, '用户名不能为纯数字');
        }

        if (!Session::get('registerCaptchaPass', false)) {
            if (!CaptchaFacade::check($captcha)) {
                SessionUtil::atomicProduce('registerCaptchaPassCount', 1);
                return Response::generate(-1, '请重新进行安全验证');
            }
        }
        if (!SessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::generate(-1, '请进行安全验证');
        }

        if (modstart_config('registerPhoneEnable')) {
            if (empty($phone)) {
                return Response::generate(-1, '请输入手机');
            }
            $phoneVerifyCheck = Session::get('registerPhoneVerify');
            if ($phoneVerify != $phoneVerifyCheck) {
                Log::info('Member.Register.PhoneVerifyError - ' . $phoneVerify . ' - ' . $phoneVerifyCheck);
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
            $emailVerifyCheck = Session::get('registerEmailVerify');
            if ($emailVerify != $emailVerifyCheck) {
                Log::info('Member.Register.EmailVerifyError - ' . $emailVerify . ' - ' . $emailVerifyCheck);
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
            /** @var AbstractMemberRegisterProcessorProvider $provider */
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
        $update['registerIp'] = StrUtil::mbLimit(Request::ip(), 20);
        if (!empty($update)) {
            MemberUtil::update($memberUserId, $update);
        }
        EventUtil::fire(new MemberUserRegisteredEvent($memberUserId));
        Session::forget('registerCaptchaPass');
        foreach (MemberRegisterProcessorProvider::listAll() as $provider) {
            /** @var AbstractMemberRegisterProcessorProvider $provider */
            $provider->postProcess($memberUserId);
        }
        return Response::generate(0, '注册成功', [
            'id' => $memberUserId,
        ]);
    }

    /**
     * @return array
     * @Api 注册-获取注册邮箱验证码
     * @ApiBodyParam target string 邮箱地址
     */
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
            return Response::generate(-1, '请先进行安全验证');
        }
        if (!SessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::generate(-1, '请进行安全验证');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (!empty($memberUser)) {
            return Response::generate(-1, '邮箱已经被占用');
        }

        if (Session::get('registerEmailVerifyTime') && $email == Session::get('registerEmail')) {
            if (Session::get('registerEmailVerifyTime') + 60 > time()) {
                return Response::generate(-1, '验证码发送频繁，请稍后再试!');
            }
        }

        $verify = rand(100000, 999999);

        MailSendJob::create($email, '注册账户验证码', 'verify', ['code' => $verify]);

        Session::put('registerEmailVerify', $verify);
        Session::put('registerEmailVerifyTime', time());
        Session::put('registerEmail', $email);

        return Response::generate(0, '验证码发送成功');
    }


    /**
     * @return array
     * @Api 注册-获取注册手机验证码
     * @ApiBodyParam target string 手机号
     */
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
            return Response::generate(-1, '请先进行安全验证');
        }
        if (!SessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::generate(-1, '请进行安全验证');
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

        SmsSendJob::create($phone, 'verify', ['code' => $verify]);

        Session::put('registerPhoneVerify', $verify);
        Session::put('registerPhoneVerifyTime', time());
        Session::put('registerPhone', $phone);

        return Response::generate(0, '验证码发送成功');
    }

    /**
     * @return array
     *
     * @Api 注册-图片验证码验证
     * @ApiBodyParam captcha string 图片验证码
     */
    public function registerCaptchaVerify()
    {
        $provider = SecurityUtil::registerCaptchaProvider();
        if ($provider) {
            $ret = $provider->validate();
            if (Response::isError($ret)) {
                return $ret;
            }
        } else {
            $input = InputPackage::buildFromInput();
            $captcha = $input->getTrimString('captcha');
            if (!CaptchaFacade::check($captcha)) {
                SessionUtil::atomicRemove('registerCaptchaPassCount');
                return Response::generate(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
            }
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

    public function oauthBindCaptchaVerify()
    {
        $input = InputPackage::buildFromInput();
        $captcha = $input->getTrimString('captcha');
        if (!CaptchaFacade::check($captcha)) {
            SessionUtil::atomicRemove('oauthBindCaptchaPassCount');
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
        }
        Session::put('oauthBindCaptchaPass', true);
        $passCount = 1;
        if (modstart_config('Member_OauthBindPhoneEnable')) {
            $passCount++;
        }
        if (modstart_config('Member_OauthBindEmailEnable')) {
            $passCount++;
        }
        SessionUtil::atomicProduce('oauthBindCaptchaPassCount', $passCount);
        return Response::generateSuccess();
    }

    public function oauthBindCaptchaRaw()
    {
        return CaptchaFacade::create('default');
    }

    public function oauthBindCaptcha()
    {
        Session::forget('oauthBindCaptchaPass');
        $captcha = $this->oauthBindCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }

    /**
     * @return array
     * @Api 授权登录-获取注册邮箱验证码
     * @ApiBodyParam target string 邮箱地址
     */
    public function oauthBindEmailVerify()
    {
        if (!modstart_config('Member_OauthBindEmailEnable')) {
            return Response::generate(-1, '授权登录未开启邮箱');
        }
        $input = InputPackage::buildFromInput();

        $email = $input->getEmail('target');
        if (empty($email)) {
            return Response::generate(-1, '邮箱不能为空');
        }

        if (!Session::get('oauthBindCaptchaPass', false)) {
            return Response::generate(-1, '请先进行安全验证');
        }
        if (!SessionUtil::atomicConsume('oauthBindCaptchaPassCount')) {
            return Response::generate(-1, '请进行安全验证');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (!empty($memberUser)) {
            return Response::generate(-1, '邮箱已经被占用');
        }

        if (Session::get('oauthBindEmailVerifyTime') && $email == Session::get('oauthBindEmail')) {
            if (Session::get('oauthBindEmailVerifyTime') + 60 > time()) {
                return Response::generate(-1, '验证码发送频繁，请稍后再试!');
            }
        }

        $verify = rand(100000, 999999);

        MailSendJob::create($email, '注册账户验证码', 'verify', ['code' => $verify]);

        Session::put('oauthBindEmailVerify', $verify);
        Session::put('oauthBindEmailVerifyTime', time());
        Session::put('oauthBindEmail', $email);

        return Response::generate(0, '验证码发送成功');
    }


    /**
     * @return array
     * @Api 授权登录-获取注册手机验证码
     * @ApiBodyParam target string 手机号
     */
    public function oauthBindPhoneVerify()
    {
        if (!modstart_config('Member_OauthBindPhoneEnable')) {
            return Response::generate(-1, '注册未开启手机');
        }
        $input = InputPackage::buildFromInput();

        $phone = $input->getPhone('target');
        if (empty($phone)) {
            return Response::generate(-1, '手机不能为空');
        }

        if (!Session::get('oauthBindCaptchaPass', false)) {
            return Response::generate(-1, '请先进行安全验证');
        }
        if (!SessionUtil::atomicConsume('oauthBindCaptchaPassCount')) {
            return Response::generate(-1, '请进行安全验证');
        }

        $memberUser = MemberUtil::getByPhone($phone);
        if (!empty($memberUser)) {
            return Response::generate(-1, '手机已经被占用');
        }

        if (Session::get('oauthBindPhoneVerifyTime') && $phone == Session::get('oauthBindPhone')) {
            if (Session::get('oauthBindPhoneVerifyTime') + 60 > time()) {
                return Response::generate(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);

        SmsSendJob::create($phone, 'verify', ['code' => $verify]);

        Session::put('oauthBindPhoneVerify', $verify);
        Session::put('oauthBindPhoneVerifyTime', time());
        Session::put('oauthBindPhone', $phone);

        return Response::generate(0, '验证码发送成功');
    }

    public function registerCaptchaRaw()
    {
        return CaptchaFacade::create('default');
    }


    /**
     * @return array
     *
     * @Api 注册-获取注册验证码图片
     * @ApiResponseData {
     *   "image":"图片Base64"
     * }
     */
    public function registerCaptcha()
    {
        Session::forget('registerCaptchaPass');
        $captcha = $this->registerCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }

    /**
     * @return array
     *
     * @Api 找回密码-根据手机号找回
     * @ApiBodyParam phone string 手机号
     * @ApiBodyParam verify string 手机验证码
     */
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
        $verifyCheck = Session::get('retrievePhoneVerify');
        if ($verify != $verifyCheck) {
            Log::info('Member.RetrievePhone.PhoneVerifyError - ' . $verify . ' - ' . $verifyCheck);
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

    /**
     * @return array
     *
     * @Api 找回密码-发送手机验证码
     * @ApiBodyParam target string 手机号
     * @ApiBodyParam captcha string 图片验证码
     */
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
        if (!CaptchaFacade::check($captcha)) {
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

        SmsSendJob::create($phone, 'verify', ['code' => $verify]);

        return Response::generate(0, '验证码发送成功');
    }

    /**
     * @return array
     *
     * @Api 找回密码-根据邮箱找回
     * @ApiBodyParam email string 邮箱
     * @ApiBodyParam verify string 邮箱验证码
     */
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
        $verifyCheck = Session::get('retrieveEmailVerify');
        if ($verify != $verifyCheck) {
            Log::info('Member.RetrieveEmail.PhoneVerifyError - ' . $verify . '- ' . $verifyCheck);
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

    /**
     * @return array
     *
     * @Api 找回密码-发送邮箱验证码
     * @ApiBodyParam target string 邮箱地址
     * @ApiBodyParam captcha string 图片验证码
     */
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
        if (!CaptchaFacade::check($captcha)) {
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

        MailSendJob::create($email, '找回密码验证码', 'verify', ['code' => $verify]);

        Session::put('retrieveEmailVerify', $verify);
        Session::put('retrieveEmailVerifyTime', time());
        Session::put('retrieveEmail', $email);

        return Response::generate(0, '验证码发送成功');
    }

    /**
     * @return array
     *
     * @Api 找回密码-获取已验证账户信息
     * @ApiResponseData {
     *   "memberUser":{
     *     "username": "用户名"
     *   }
     * }
     */
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

    /**
     * @return array
     *
     * @Api 找回密码-重置密码
     * @ApiBodyParam password string 新密码
     * @ApiBodyParam passwordRepeat string 重复新密码
     */
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
        EventUtil::fire(new MemberUserPasswordResetedEvent($memberUser['id'], $password));
        Session::forget('retrieveMemberUserId');
        return Response::generate(0, '成功设置新密码,请您登录');
    }

    public function retrieveCaptchaRaw()
    {
        return CaptchaFacade::create('default');
    }

    /**
     * @return array
     *
     * @Api 找回密码-图片验证码
     * @ApiResponseData {
     *   "image":"验证码图片Base64"
     * }
     */
    public function retrieveCaptcha()
    {
        $captcha = $this->retrieveCaptchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }
}
