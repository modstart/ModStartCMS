<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminCRUD;
use ModStart\Admin\Model\AdminLog;
use ModStart\Admin\Type\AdminLogType;
use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;

class AdminLogController extends Controller
{
    use HasAdminCRUD;

    protected function grid()
    {
        $grid = new Grid(AdminLog::class, function (Grid $grid) {
            $grid->display('id', L('ID'))->width(80);
            $grid->select('adminUserId', L('Admin User'))->optionModel('admin_user', 'id', 'username');
            $grid->type('type', L('Type'))->type(AdminLogType::class)->width(100);
            $grid->display('summary', L('Title'));
            $grid->jsonKeyValue('data.content', L('Data'))->width(200);
            $grid->display('created_at', L('Created At'))->width(160);
            $grid->gridFilter(function (GridFilter $filter) {
                $filter->eq('adminUserId', L('Admin User'))->selectModel('admin_user', 'id', 'username');
                $filter->like('summary', L('Title'));
                $filter->eq('type', L('Type'))->radio(AdminLogType::class);
            });
        });
        $grid->canAdd(false)->canEdit(false);
        $grid->canDelete(AdminPermission::permit('AdminLogManage'));
        $grid->canMultiSelectItem(true)->canBatchDelete(true);
        $grid->title(L('Admin Log'));
        return $grid;
    }

    protected function form()
    {
        $form = new Form(AdminLog::class, function (Form $form) {
            $form->display('id', L('ID'))->editable(true);
        });
        return $form;
    }

    protected function detail()
    {
        $detail = new Detail(AdminLog::class, function (Detail $detail) {
            $detail->display('id', L('ID'));
            $detail->display('created_at', L('Created At'));
            $detail->type('type', L('Type'))->type(AdminLogType::class);
            $detail->display('summary', L('Title'));
            $detail->jsonKeyValue('data.content', L('Data'));
        });
        $detail->title(L('Admin Log'));
        return $detail;
    }
}
