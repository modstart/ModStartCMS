<?php


namespace Module\Vendor\Admin\Controller;


use Edwin404\Base\Support\InputPackage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Form\Form;
use ModStart\Widget\Box;

class SecurityController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    public function secondVerify(AdminPage $page)
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', modstart_admin_url(''));
        $form = Form::make('');
        $form->password('password', '安全验证密码');
        $form->showReset(false);
        $form->formClass('wide');
        return $page->pageTitle('二次安全验证')
            ->body(Box::make($form, '二次安全验证'))
            ->handleForm($form, function (Form $form) use ($redirect) {
                $data = $form->dataForming();
                $password = $data['password'];
                $passwordCorrectMd5 = modstart_config('Vendor_SecuritySecondVerifyPassword');
                BizException::throwsIf('密码不正确', md5($password) != $passwordCorrectMd5);
                Session::set('Vendor_SecuritySecondVerifyTime', time() + 3600);
                return Response::send(0, null, null, $redirect);
            });
    }
}
