<?php


namespace Module\MemberOauth\Oauth;


use Illuminate\Support\Facades\Input;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\AgentUtil;
use Module\Member\Oauth\AbstractOauth;
use Module\Member\Util\MemberUtil;
use Overtrue\Socialite\SocialiteManager;

class WechatOauth extends AbstractOauth
{
    public function isSupport()
    {
        return !AgentUtil::isWechat();
    }

    public function name()
    {
        return 'wechat';
    }

    public function title()
    {
        return '微信扫码';
    }

    public function render()
    {
        return '<a href="' . modstart_web_url('oauth_login_wechat', ['redirect' => Input::get('redirect', modstart_web_url(''))]) . '" class="wechat"><i class="iconfont icon-wechat"></i></a>';
    }

    private function proxy()
    {
        return modstart_config()->getWithEnv('oauthWechatProxy');
    }

    private function socialite($param)
    {
        $socialite = new SocialiteManager([
            'wechat' => [
                'client_id' => modstart_config()->getWithEnv('oauthWechatAppId'),
                'client_secret' => modstart_config()->getWithEnv('oauthWechatAppSecret'),
                'redirect' => isset($param['callback']) ? $param['callback'] : null,
            ]
        ]);
        $socialite = $socialite->create('wechat');
        return $socialite;
    }

    public function processRedirect($param)
    {
        $socialite = $this->socialite($param);
        $url = $socialite->redirect();
        if ($this->proxy()) {
            $url = $this->proxy() . '?_proxy=' . urlencode($url);
        }
        return Response::generate(0, 'ok', [
            'redirect' => $url,
        ]);
    }

    public function processLogin($param)
    {
        $socialite = $this->socialite($param);
        if ($this->proxy()) {
            $socialite->redirect($this->proxy());
        }
        $openid = null;
        try {
            $user = $socialite->userFromCode($param['code']);
            $openid = $user->getId();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return Response::generateError('登录失败(' . $msg . ')', null, '/');
        }
        if (empty($openid)) {
            return Response::generate(-1, '登录失败(openid=' . $openid . ')', null, '/');
        }
        $raw = $user->getRaw();
        $userInfo = [
            'username' => $user->getName(),
            'avatar' => $user->getAvatar(),
            'openid' => $openid,
            'unionid' => !empty($raw['unionid']) ? $raw['unionid'] : null,
        ];
        return Response::generateSuccessData([
            'userInfo' => $userInfo,
        ]);
    }

    public function processTryLogin($param)
    {
        $userInfo = $param['userInfo'];
        $openid = $userInfo['openid'];
        if (!empty($userInfo['unionid'])) {
                        $memberUserId = MemberUtil::getIdByOauthAndCheck('wechatunion', $userInfo['unionid']);
            if ($memberUserId) {
                MemberUtil::putOauth($memberUserId, $this->name(), $openid);
                return Response::generateSuccessData([
                    'memberUserId' => $memberUserId,
                ]);
            }
        }
        $memberUserId = MemberUtil::getIdByOauthAndCheck($this->name(), $openid);
        if ($memberUserId) {
            return Response::generateSuccessData([
                'memberUserId' => $memberUserId,
            ]);
        }
        return Response::generateSuccessData(['memberUserId' => 0]);
    }

    public function processBindToUser($param)
    {
        $memberUserId = $param['memberUserId'];
        $userInfo = $param['userInfo'];
        $info = [];
        if (!empty($userInfo['username'])) {
            $info['infoUsername'] = $userInfo['username'];
        }
        if (!empty($userInfo['avatar'])) {
            $info['infoAvatar'] = $userInfo['avatar'];
        }
        if (!empty($userInfo['unionid'])) {
                        $id = MemberUtil::getIdByOauthAndCheck('wechatunion', $userInfo['unionid']);
            if ($id && $memberUserId != $id) {
                MemberUtil::forgetOauth('wechatunion', $userInfo['unionid']);
            }
            MemberUtil::putOauth($memberUserId, 'wechatunion', $userInfo['unionid'], $info);
        }
        $id = MemberUtil::getIdByOauthAndCheck($this->name(), $userInfo['openid']);
        if ($id && $memberUserId != $id) {
            MemberUtil::forgetOauth($this->name(), $userInfo['openid']);
        }
        MemberUtil::putOauth($memberUserId, $this->name(), $userInfo['openid'], $info);
        return Response::generateSuccess();
    }


}
