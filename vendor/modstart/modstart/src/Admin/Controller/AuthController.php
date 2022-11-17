<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Event\AdminUserLoginedEvent;
use ModStart\Admin\Event\AdminUserLogoutEvent;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\AgentUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Misc\Captcha\CaptchaFacade;

class AuthController extends Controller
{
    public function loginCaptcha()
    {
        return CaptchaFacade::create('default');
    }

    public function login()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', modstart_admin_url());
        if (Admin::id()) {
            return Response::redirect($redirect);
        }
        if (modstart_config('adminSSOClientEnable', false)) {
            return Response::redirect(modstart_admin_url('sso/client', [
                'redirect' => $redirect,
            ]));
        }
        /**
         * 获取人机检测Provider
         */
        $captchaProviderName = modstart_config('AdminManagerEnhance_LoginCaptchaProvider', null);
        $captchaProvider = null;
        if (class_exists(\Module\Vendor\Provider\Captcha\CaptchaProvider::class)) {
            $captchaProvider = \Module\Vendor\Provider\Captcha\CaptchaProvider::get($captchaProviderName);
            if ($captchaProvider) {
                $captchaProvider->setParam('biz', 'admin');
            }
        }
        if (Request::isPost()) {
            $input = InputPackage::buildFromInput();

            $isSmsCaptchaQuickLogin = (
                config('modstart.admin.login.captcha', false)
                && $captchaProvider
                && $captchaProvider->name() == 'sms'
                && modstart_config('AdminManagerEnhance_SmsCaptchaQuick', false)
            );
            if ($isSmsCaptchaQuickLogin) {
                // do nothing
            } else {
                $username = $input->getTrimString('username');
                $password = $input->getTrimString('password');
                if (empty($username)) {
                    return Response::json(-1, L('Username Required'));
                }
                if (empty($password)) {
                    return Response::json(-2, L('Password Required'));
                }
            }
            if (config('modstart.admin.login.captcha', false)) {
                if ($captchaProvider) {
                    $ret = $captchaProvider->validate();
                    if (Response::isError($ret)) {
                        return Response::jsonFromGenerate($ret);
                    }
                } else {
                    if (!CaptchaFacade::check($input->getTrimString('captcha'))) {
                        return Response::json(-1, L('Captcha Incorrect'), null, "[js]$('[data-captcha]').click();");
                    }
                }
            }
            if ($isSmsCaptchaQuickLogin) {
                /** @var $smsCaptchaProvider \Module\CaptchaSms\Provider\SmsCaptchaProvider */
                $smsCaptchaProvider = $captchaProvider;
                $phone = $smsCaptchaProvider->getVerifiedPhone();
                $ret = Admin::loginByPhone($phone);
                if ($ret['code']) {
                    Admin::addErrorLog(0, L('Login Error'), [
                        'IP' => Request::ip(),
                        L('Phone') => $phone,
                    ]);
                    return Response::json(-1, L('登录失败'), null, "[js]$('[data-captcha]').click();");
                }
            } else {
                $ret = Admin::login($username, $password);
                if ($ret['code']) {
                    Admin::addErrorLog(0, L('Login Error'), [
                        'IP' => Request::ip(),
                        L('Username') => $username,
                        L('Password') => '******',
                    ]);
                    return Response::json(-1, L('Username / Password Incorrect'), null, "[js]$('[data-captcha]').click();");
                }
            }
            $adminUser = $ret['data'];
            Session::put(Admin::ADMIN_USER_ID_SESSION_KEY, $adminUser['id']);
            Session::forget('_adminUserPasswordWeak');
            if ($isSmsCaptchaQuickLogin) {
                // do nothing
            } else {
                if (strlen($password) < 6 && StrUtil::passwordStrength($password) <= 1) {
                    Session::put('_adminUserPasswordWeak', true);
                }
            }
            Admin::addInfoLog($adminUser['id'], L('Login Success'), [
                'IP' => Request::ip(),
            ]);
            AdminUserLoginedEvent::fire($adminUser['id'], Request::ip(), AgentUtil::getUserAgent());
            $redirect = $input->getTrimString('redirect', modstart_admin_url());
            return Response::redirect($redirect);
        }
        return view('modstart::admin.login', [
            'pageTitle' => L('Admin Login'),
            'captchaProviderName' => $captchaProviderName,
            'captchaProvider' => $captchaProvider,
            'redirect' => $redirect,
        ]);
    }

    public function loginQuick()
    {
        BizException::throwsIf('快速登录未开启', !config('env.ADMIN_LOGIN_QUICK_ENABLE', false));
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $ts = $input->getInteger('ts');
        BizException::throwsIf('请求超时', $ts < time() - 1800 || $ts > time() + 1800);
        $adminUser = Admin::get($id);
        BizException::throwsIfEmpty('登录失败', $adminUser);
        $sign = md5($id . ':' . $ts . ':' . $adminUser['password'] . ':' . $adminUser['passwordSalt']);
        BizException::throwsIf('登录失败', $sign != $input->getTrimString('sign'));
        Session::put(Admin::ADMIN_USER_ID_SESSION_KEY, $adminUser['id']);
        Admin::addInfoLog($adminUser['id'], L('Login Success'), [
            'IP' => Request::ip(),
        ]);
        $redirect = $input->getTrimString('redirect', modstart_admin_url());
        return Response::redirect($redirect);
    }

    public function logout()
    {
        $adminUserId = Admin::id();
        Session::forget(Admin::ADMIN_USER_ID_SESSION_KEY);
        AdminUserLogoutEvent::fire($adminUserId, Request::ip(), AgentUtil::getUserAgent());
        if (modstart_config('adminSSOClientEnable', false)) {
            $input = InputPackage::buildFromInput();
            if ($input->getTrimString('server', '') != 'true') {
                $ssoServer = modstart_config('adminSSOServer');
                if (empty($ssoServer)) {
                    return Response::send(-1, L('Config adminSSOServer missing'));
                }
                $clientRedirect = $input->getTrimString('redirect', '/');
                $clientLogout = Request::domainUrl() . '/logout?server=true&redirect=' . urlencode($clientRedirect);
                $ssoServerLogout = $ssoServer . '_logout?redirect=' . urlencode($clientLogout);
                return Response::redirect($ssoServerLogout);
            }
        }
        return Response::redirect(modstart_admin_url());
    }

    public function ssoClient()
    {
        if (!modstart_config('adminSSOClientEnable', false)) {
            return Response::send(-1, L('Config adminSSOClientEnable disabled'));
        }
        $ssoServer = modstart_config('adminSSOServer', '');
        if (empty($ssoServer)) {
            return Response::send(-1, L('Config adminSSOServer missing'));
        }

        $ssoSecret = modstart_config('adminSSOClientSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, L('Config adminSSOClientSecret missing'));
        }

        $input = InputPackage::buildFromInput();
        $server = $input->getTrimString('server');
        if ($server) {
            $username = @base64_decode($input->getTrimString('username'));
            $timestamp = $input->getTrimString('timestamp');
            $sign = $input->getTrimString('sign');
            if (empty($username)) {
                return Response::send(-1, '同步登录返回的用户名为空');
            }
            if (empty($timestamp)) {
                return Response::send(-1, 'Sign required');
            }
            if (empty($sign)) {
                return Response::send(-1, 'Sign required');
            }
            $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($server) . md5($username));
            if ($sign != $signCalc) {
                return Response::send(-1, 'Sign error');
            }
            if (abs(time() - $timestamp) > 2400 * 2600) {
                return Response::send(-1, 'Timestamp error');
            }

            if ($server != $ssoServer) {
                return Response::send(-1, 'Server not match ' . $ssoServer);
            }
            $adminUser = Admin::getByUsername($username);
            if (empty($adminUser)) {
                $adminUser = Admin::add($username, null, true);
                $adminUser = Admin::get($adminUser['id']);
            }
            Session::put('_adminUserId', $adminUser['id']);
            $ssoRedirect = Session::get('adminSSORedirect', null);
            if (empty($ssoRedirect)) {
                return Response::send(0, 'Login success, adminSSORedirect missing');
            }
            return Response::redirect($ssoRedirect);

        } else {

            $redirect = trim($input->getTrimString('redirect'));
            Session::put('adminSSORedirect', $redirect);

            $client = Request::domainUrl() . modstart_admin_url('sso/client');
            $timestamp = time();
            $sign = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));
            $redirect = $ssoServer . '?client=' . urlencode($client) . '&timestamp=' . $timestamp . '&sign=' . $sign;
            return Response::redirect($redirect);
        }
    }

    public function ssoServer()
    {
        $input = InputPackage::buildFromInput();
        $client = trim($input->getTrimString('client'));
        $timestamp = intval($input->getTrimString('timestamp'));
        $sign = trim($input->getTrimString('sign'));
        if (empty($client)) {
            return Response::send(-1, 'client empty');
        }
        if (empty($timestamp)) {
            return Response::send(-1, 'timestamp empty');
        }
        if (empty($sign)) {
            return Response::send(-1, 'sign empty');
        }
        if (!modstart_config('adminSSOServerEnable', false)) {
            return Response::send(-1, 'adminSSOServerEnable disabled');
        }
        $ssoSecret = modstart_config('adminSSOServerSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, 'adminSSOServerSecret missing');
        }
        $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));
        if ($sign != $signCalc) {
            return Response::send(-1, 'sign error');
        }
        if (abs(time() - $timestamp) > 2400 * 2600) {
            return Response::send(-1, 'timestamp error');
        }
        $ssoClientList = explode("\n", modstart_config('adminSSOClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::send(-1, 'server client missing : ' . $client);
        }
        Session::put('adminSSOClient', $client);

        if (Session::get('_adminUserId', 0)) {
            return Response::redirect(modstart_admin_url('sso/server_success'));
        }

        return Response::redirect(modstart_admin_url('login?redirect=' . urlencode(modstart_admin_url('sso/server_success'))));
    }

    public function ssoServerSuccess()
    {
        if (!Session::get('_adminUserId', 0)) {
            return Response::redirect(modstart_admin_url('login?redirect=' . urlencode(modstart_admin_url('sso/server_success'))));
        }
        $adminUser = Admin::get(Session::get('_adminUserId', 0));

        $ssoSecret = modstart_config('adminSSOServerSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, 'adminSSOServerSecret missing');
        }

        $server = Request::domainUrl() . modstart_admin_url('sso/server');
        $timestamp = time();
        $username = $adminUser['username'];
        $sign = md5(md5($ssoSecret) . md5($timestamp . '') . md5($server) . md5($username));

        $ssoClient = Session::get('adminSSOClient', '');
        if (empty($ssoClient)) {
            return Response::send(0, 'adminSSOClient missing');
        }
        Session::forget('adminSSOClient', $ssoClient);

        $redirect = $ssoClient . '?server=' . urlencode($server) . '&timestamp=' . $timestamp
            . '&username=' . urlencode(base64_encode($username)) . '&sign=' . $sign;

        return Response::redirect($redirect);
    }

    public function ssoServerLogout()
    {
        $input = InputPackage::buildFromInput();
        Session::forget('_adminUserId');
        $redirect = $input->getTrimString('redirect', modstart_admin_url());
        return Response::redirect($redirect);
    }
}
