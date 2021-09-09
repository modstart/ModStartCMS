<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Exception\ResultException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Form\Form;
use ModStart\Layout\Row;
use ModStart\Widget\Box;

class ProfileController extends Controller
{
    public function changePassword(AdminPage $adminPage)
    {
        $form = new Form(DynamicModel::class);
        $form->password('password', L('Password'))->rules('required');
        $form->password('passwordNew', L('New Password'))->rules('required');
        $form->password('passwordRepeat', L('Repeat Password'))->rules('required');
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->formRequest(function (Form $form) {
                $data = $form->dataForming();
                ResultException::throwsIf(L('New Password Not Match'), $data['passwordNew'] != $data['passwordRepeat']);
                $ret = Admin::changePassword(Admin::id(), $data['password'], $data['passwordNew']);
                ResultException::throwsIfFail($ret);
                Admin::addInfoLog(Admin::id(), L('Change password'));
                return Response::json(0, L('Password change correct, please relogin'), null, action('\ModStart\Admin\Controller\AuthController@logout'));
            });
        }
        return $adminPage
            ->pageTitle(L('Change Password'))
            ->row(function (Row $row) use ($form) {
                $row->column(6, new Box($form, L('Change Password')));
            });
    }
}
