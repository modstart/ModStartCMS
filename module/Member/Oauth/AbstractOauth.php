<?php


namespace Module\Member\Oauth;


use ModStart\Core\Input\Response;
use Module\Member\Util\MemberUtil;

abstract class AbstractOauth
{
    public function hasRender()
    {
        return true;
    }

    public function isSupport()
    {
        return true;
    }

    public function color()
    {
        return null;
    }

    public function title()
    {
        return $this->name();
    }

    public function oauthKey()
    {
        return $this->name();
    }

    abstract public function name();

    abstract public function render();

    public function bindRender()
    {
        return null;
    }

    abstract public function processRedirect($param);

    abstract public function processLogin($param);

    public function processTryLogin($param)
    {
        $userInfo = $param['userInfo'];
        $openid = $userInfo['openid'];
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
        $id = MemberUtil::getIdByOauthAndCheck($this->name(), $userInfo['openid']);
        if ($id && $memberUserId != $id) {
            MemberUtil::forgetOauth($this->name(), $userInfo['openid']);
        }
        $info = [];
        if (!empty($userInfo['username'])) {
            $info['infoUsername'] = $userInfo['username'];
        }
        if (!empty($userInfo['avatar'])) {
            $info['infoAvatar'] = $userInfo['avatar'];
        }
        MemberUtil::putOauth($memberUserId, $this->name(), $userInfo['openid'], $info);
        return Response::generateSuccess();
    }
}
