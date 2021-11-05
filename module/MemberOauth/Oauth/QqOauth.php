<?php


namespace Module\MemberOauth\Oauth;


use Illuminate\Support\Facades\Input;
use ModStart\Core\Input\Response;
use Module\Member\Oauth\AbstractOauth;
use Module\Member\Util\MemberUtil;
use Overtrue\Socialite\SocialiteManager;

class QqOauth extends AbstractOauth
{
    public function name()
    {
        return 'qq';
    }

    public function title()
    {
        return 'QQ授权';
    }

    public function render()
    {
        return '<a href="' . modstart_web_url('oauth_login_qq', ['redirect' => Input::get('redirect', modstart_web_url(''))]) . '" class="qq"><i class="iconfont icon-qq"></i></a>';
    }

    private function proxy()
    {
        return modstart_config()->getWithEnv('oauthQQProxy');
    }

    private function socialite($param)
    {
        $socialite = new SocialiteManager([
            'qq' => [
                'client_id' => modstart_config()->getWithEnv('oauthQQKey'),
                'client_secret' => modstart_config()->getWithEnv('oauthQQAppSecret'),
                'redirect' => isset($param['callback']) ? $param['callback'] : null,
            ]
        ]);
        $socialite = $socialite->create('qq');
        return $socialite;
    }

    public function processRedirect($param)
    {
        $socialite = $this->socialite($param);
        if ($param['silence']) {
            $socialite->scopes(['snsapi_base']);
        } else {
            $socialite->scopes(['snsapi_userinfo']);
        }
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
        ];
        return Response::generateSuccessData([
            'userInfo' => $userInfo,
        ]);
    }

}
