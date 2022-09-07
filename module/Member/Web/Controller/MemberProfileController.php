<?php


namespace Module\Member\Web\Controller;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Config\MemberOauth;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberUtil;

class MemberProfileController extends ModuleBaseController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberProfileController */
    private $api;
    private $viewMemberFrame;

    /**
     * MemberProfileController constructor.
     */
    public function __construct()
    {
        list($this->viewMemberFrame, $_) = $this->viewPaths('member.frame');
        View::share('_viewMemberFrame', $this->viewMemberFrame);
        $this->api = app(\Module\Member\Api\Controller\MemberProfileController::class);
    }


    public function password()
    {
        if (Request::isPost()) {
            return Response::jsonFromGenerate($this->api->password());
        }
        return $this->view('memberProfile.password', [
            'pageTitle' => '密码设定',
        ]);
    }

    public function nickname()
    {
        if (Request::isPost()) {
            return Response::jsonFromGenerate($this->api->nickname());
        }
        return $this->view('memberProfile.nickname', [
            'pageTitle' => '昵称修改',
        ]);
    }

    public function avatar()
    {
        if (Request::isPost()) {
            return Response::jsonFromGenerate($this->api->avatar());
        }
        return $this->view('memberProfile.avatar', [
            'pageTitle' => '修改头像',
        ]);
    }

    public function captcha()
    {
        return $this->api->captchaRaw();
    }

    public function bind()
    {
        return Response::redirect(modstart_web_url('member_profile/email'));
    }

    public function security()
    {
        return Response::redirect(modstart_web_url('member_profile/password'));
    }

    public function profile()
    {
        return Response::redirect(modstart_web_url('member_profile/avatar'));
    }

    public function oauth($type)
    {
        $oauth = MemberOauth::getOrFail($type);
        BizException::throwsIfEmpty('授权登录不存在', $oauth);
        $oauthRecord = MemberUtil::getOauth(MemberUser::id(), $oauth->oauthKey());
        // var_dump([MemberUser::id(), $oauth->oauthKey()]);
        // var_dump($oauthRecord);
        $viewData = [
            'pageTitle' => $oauth->title(),
            'oauth' => $oauth,
            'oauthRecord' => $oauthRecord,
        ];
        return $this->view('memberProfile.oauth', $viewData);
    }

    public function email()
    {
        if (Request::isPost()) {
            return Response::jsonFromGenerate($this->api->email());
        }
        return $this->view('memberProfile.email', [
            'pageTitle' => '邮箱绑定',
        ]);
    }

    public function emailVerify()
    {
        return Response::sendFromGenerate($this->api->emailVerify());
    }

    public function phone()
    {
        if (Request::isPost()) {
            return Response::jsonFromGenerate($this->api->phone());
        }
        return $this->view('memberProfile.phone', [
            'pageTitle' => '手机绑定',
        ]);
    }

    public function phoneVerify()
    {
        return Response::sendFromGenerate($this->api->phoneVerify());
    }

    public function delete()
    {
        if (Request::isPost()) {
            return Response::jsonFromGenerate($this->api->delete());
        }
        return $this->view('memberProfile.delete', [
            'pageTitle' => '注销账号',
        ]);
    }
}
