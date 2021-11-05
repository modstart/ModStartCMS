<?php


namespace Module\MemberOauth\Api\Controller;

use EasyWeChat\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\Module\ModuleBaseController;
use Module\Member\Events\MemberUserLoginedEvent;
use Module\Member\Util\MemberUtil;

class AuthController extends ModuleBaseController
{
    public function loginWechatMiniProgram()
    {
        $input = InputPackage::buildFromInput();
        $config = modstart_config();
        if (!$config->getBoolean('oauthWechatMiniProgramEnable', false)) {
            return Response::generateError('微信小程序登录未启用');
        }

        $code = $input->getTrimString('code');
        $iv = $input->getTrimString('iv');
        $encryptedData = $input->getTrimString('encryptedData');
        $view = $input->getBoolean('view', false);
        if (empty($code) || empty($iv) || empty($encryptedData)) {
            return Response::generateError('提交数据不完整');
        }
        $config = [
            'app_id' => $config->getWithEnv('oauthWechatMiniProgramAppId'),
            'secret' => $config->getWithEnv('oauthWechatMiniProgramAppSecret'),
            'response_type' => 'array',
            'log' => [
                'default' => 'debug',
                'channels' => [
                    'debug' => [
                        'driver' => 'single',
                        'path' => storage_path('logs/easywechat_mini_program_' . TimeUtil::date() . '.log'),
                        'level' => 'debug',
                    ],
                ]
            ],
        ];
        $app = Factory::miniProgram($config);
        
        $session = $app->auth->session($code);
        
        $oauthInfo = $app->encryptor->decryptData($session['session_key'], $iv, $encryptedData);
        if ($view) {
            Session::put('oauthViewOpenId_wechatminiprogram', $oauthInfo['openId']);
            return Response::generateSuccess();
        }
        $memberUserId = MemberUtil::getIdByOauthAndCheck('wechatminiprogram', $oauthInfo['openId']);
        if (!($memberUserId > 0)) {
            if (!empty($oauthInfo['unionId'])) {
                $memberUserId = MemberUtil::getIdByOauthAndCheck('wechatunion', $oauthInfo['unionId']);
            }
        }
        if (!($memberUserId > 0)) {
            $ret = MemberUtil::registerUsernameQuick($oauthInfo['nickName']);
            if ($ret['code']) {
                return Response::json(-1, $ret['msg']);
            }
            $memberUserId = $ret['data']['id'];
        }
        $memberUser = MemberUtil::get($memberUserId);
        
        if (empty($memberUser)) {
            return Response::generateError('微信小程序登录失败');
        }
        if (empty($memberUser['avatar']) && !empty($oauthInfo['avatarUrl'])) {
            $avatarContent = CurlUtil::getRaw($oauthInfo['avatarUrl']);
            if ($avatarContent) {
                MemberUtil::setAvatar($memberUserId, $avatarContent, 'jpg');
            }
        }
        if (!empty($oauthInfo['unionId'])) {
            MemberUtil::putOauth($memberUserId, 'wechatunion', $oauthInfo['unionId']);
        }
        MemberUtil::putOauth($memberUserId, 'wechatminiprogram', $oauthInfo['openId']);
        Session::put('memberUserId', $memberUser['id']);
        Event::fire(new MemberUserLoginedEvent($memberUser['id']));
        return Response::generateSuccess();
    }
}
