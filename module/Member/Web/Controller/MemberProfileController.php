<?php


namespace Module\Member\Web\Controller;


use Illuminate\Support\Facades\View;
use ModStart\App\Web\Layout\WebConfigBuilder;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Form\Form;
use ModStart\Module\ModuleBaseController;
use Module\Member\Support\MemberLoginCheck;

class MemberProfileController extends ModuleBaseController implements MemberLoginCheck
{
    
    private $api;
    private $viewMemberFrame;

    
    public function __construct()
    {
        list($this->viewMemberFrame, $_) = $this->viewPaths('member.frame');
        View::share('_viewMemberFrame', $this->viewMemberFrame);
        $this->api = app(\Module\Member\Api\Controller\MemberProfileController::class);
    }


    public function password(WebConfigBuilder $builder)
    {
        $builder->pageTitle('修改密码');
        $builder->page()->view($this->viewMemberFrame);
        $builder->password('passwordOld', '旧密码')->required()->styleFormField('max-width:10rem;');
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
