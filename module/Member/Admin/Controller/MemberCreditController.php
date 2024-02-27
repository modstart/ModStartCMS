<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Form\Form;
use Module\Member\Util\MemberCreditUtil;

class MemberCreditController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '\Module\Member\Admin\Controller\MemberController@index',
    ];

    public function charge(AdminDialogPage $page)
    {
        $memberUserId = InputPackage::buildFromInput()->getInteger('memberUserId');
        $form = Form::make('');
        $form->display('_total', '总积分')->value(MemberCreditUtil::getTotal($memberUserId))->addable(true);
        $form->number('change', '积分变更')->help('负数表示减少')->required();
        $form->text('remark', '说明')->defaultValue('系统变更')->required();
        $form->showSubmit(false)->showReset(false);
        return $page->pageTitle('积分变更')->body($form)->handleForm($form, function (Form $form) use ($memberUserId) {
            AdminPermission::demoCheck();
            $data = $form->dataForming();
            ModelUtil::transactionBegin();
            MemberCreditUtil::change($memberUserId, $data['change'], $data['remark'], [
                'adminUserId' => Admin::id(),
            ]);
            ModelUtil::transactionCommit();
            return Response::redirect(CRUDUtil::jsDialogCloseAndParentRefresh());
        });
    }
}
