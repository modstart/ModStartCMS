<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminCRUD;
use ModStart\Admin\Model\AdminLog;
use ModStart\Admin\Type\AdminLogType;
use ModStart\Detail\Detail;
use ModStart\Field\AbstractField;
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
            $grid->display('created_at', L('Created At'))->width(160);
            $grid->type('type', L('Type'))->type(AdminLogType::class)->width(100);
            $grid->display('summary', L('Title'));
            $grid->code('data', L('Data'))->hookValueUnserialize(function ($value, AbstractField $field) {
                if (!$value) return '';
                return @json_encode(json_decode($value->content, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
            $grid->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
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
            $detail->code('data', L('Data'))->hookValueUnserialize(function ($value, AbstractField $field) {
                if (!$value) return '';
                return json_encode(json_decode($value->content, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
        });
        $detail->title(L('Admin Log'));
        return $detail;
    }
}
