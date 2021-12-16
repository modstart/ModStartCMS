<?php


namespace Module\Member\Web\Controller;


use Illuminate\Support\Facades\View;
use ModStart\App\Web\Layout\WebConfigBuilder;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;
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


    public function password(WebConfigBuilder $builder)
    {
        $memberUser = MemberUser::get();
        $builder->pageTitle('修改密码');
        $builder->page()->view($this->viewMemberFrame);
        if (empty($memberUser['password'])) {
            $builder->custom('tips', '')->hookRendering(function (AbstractField $field, $item, $index) {
                return AutoRenderedFieldValue::make('<div class="ub-alert ub-alert-warning"><i class="iconfont icon-warning"></i> 您还没有设定密码，请设定新密码</div>');
            });
        } else {
            $builder->password('passwordOld', '旧密码')->required()->styleFormField('max-width:10rem;');
        }
        $builder->password('passwordNew', '新密码')->rules('required')->styleFormField('max-width:10rem;');
        $builder->password('passwordRepeat', '重复密码')->rules('required')->styleFormField('max-width:10rem;');
        return $builder->perform(null, function (Form $form) {
            return $this->api->password();
        });
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
        $oauth = MemberOauth::get($type);
        BizException::throwsIfEmpty('授权登录不存在', $oauth);
        $oauthRecord = MemberUtil::getOauth(MemberUser::id(), $oauth->name());
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
            'pageTitle' => '修改邮箱',
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
            'pageTitle' => '修改手机',
        ]);
    }

    public function phoneVerify()
    {
        return Response::sendFromGenerate($this->api->phoneVerify());
    }
}
