<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminCRUD;
use ModStart\Admin\Model\AdminUser;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\ConvertUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Detail\Detail;
use ModStart\Field\AbstractField;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;

class AdminUserController extends Controller
{
    use HasAdminCRUD;

    protected function grid()
    {
        $grid = new Grid(AdminUser::class, function (Grid $grid) {
            $grid->display('id', L('ID'))->sortable(true)->width(80);
            $grid->text('username', L('Username'));
            if (modstart_config('AdminManagerEnhance_EnablePhone', false)) {
                $grid->text('phone', L('Phone'));
            }
            if (modstart_config('AdminManagerEnhance_EnableEmail', false)) {
                $grid->text('email', L('Email'));
            }
            $grid->tags('roles', L('Roles'))->hookFormatValue(function ($value, AbstractField $field) {
                $item = $field->item();
                /** @var \stdClass $item */
                if (AdminPermission::isFounder($item->id)) {
                    return [L('Admin Founder')];
                }
                return collect($value)->pluck('name')->toArray();
            });
            $grid->text('lastLoginTime', L('Last Login Time'));
            $grid->text('lastLoginIp', L('Last Login Ip'));
            $grid->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('username', L('Username'));
            });
            $grid->hookItemOperateRendering(function (ItemOperate $itemOperate) {
                if (AdminPermission::isFounder($itemOperate->item()->id)) {
                    $itemOperate->canDelete(false);
                }
            });
        });
        if (AdminPermission::isNotPermit('AdminUserManage')) {
            $grid->canAdd(false)->canEdit(false)->canDelete(false);
        }
        $grid->title(L('Admin User'));
        return $grid;
    }

    protected function form()
    {
        $form = new Form(AdminUser::class, function (Form $form) {
            $form->text('username', L('Username'))->rules('required|unique:admin_user,username,' . CRUDUtil::id());
            $form->text('password', L('Password'))->rules($form->isModeAdd() ? 'required' : '')
                ->placeholder($form->isModeAdd() ? '' : L('Keep Old Password If Empty'))
                ->value($form->isModeAdd() ? RandomUtil::string(6) : '')
                ->hookFormatValue(function ($value, AbstractField $field) {
                    return '';
                });
            if (modstart_config('AdminManagerEnhance_EnablePhone', false)) {
                $form->text('phone', L('Phone'))->ruleUnique('admin_user');
            }
            if (modstart_config('AdminManagerEnhance_EnableEmail', false)) {
                $form->text('email', L('Email'))->ruleUnique('admin_user');
            }
            /** @var AdminUser $item */
            $item = $form->item();
            $rolesField = $form->checkbox('roles', L('Roles'))
                ->optionModel('admin_role')
                ->hookValueUnserialize(function ($value, AbstractField $field) {
                    return $value->map(function ($r) {
                        return $r['id'];
                    });
                })
                ->hookValueSerialize(function ($value, AbstractField $field) {
                    return ConvertUtil::toArray($value);
                });
            if ($form->isModeEdit() && AdminPermission::isFounder($item->id)) {
                $rolesField->editable(false);
            }
            $form->display('created_at', L('Created At'))->editable(true);
            $form->hookSaving(function (Form $form) {
                if ($form->isModeAdd()) {
                    $data = $form->dataAdding();
                    $data['passwordSalt'] = Str::random(16);
                    $data['password'] = Admin::passwordEncrypt($data['password'], $data['passwordSalt']);
                    $form->dataAdding($data);
                } else if ($form->isModeEdit()) {
                    $data = $form->dataEditing();
                    if ($data['password']) {
                        $data['passwordSalt'] = Str::random(16);
                        $data['password'] = Admin::passwordEncrypt($data['password'], $data['passwordSalt']);
                    } else {
                        unset($data['password']);
                    }
                    $form->dataEditing($data);
                }
            });
            $form->hookDeleting(function (Form $form) {
                $form->item()->each(function ($item) {
                    if (AdminPermission::isFounder($item->id)) {
                        BizException::throws(L('Admin Founder Delete Forbidden'));
                    }
                });
            });
        });
        if (AdminPermission::isNotPermit('AdminUserManage')) {
            $form->canAdd(false)->canEdit(false)->canDelete(false);
        }
        $form->title(L('Admin User'));
        return $form;
    }

    protected function detail()
    {
        $detail = new Detail(AdminUser::class, function (Detail $detail) {
            $detail->display('id', L('ID'));
            $detail->text('username', L('Username'));
            if (modstart_config('AdminManagerEnhance_EnablePhone', false)) {
                $detail->text('phone', L('Phone'));
            }
            if (modstart_config('AdminManagerEnhance_EnableEmail', false)) {
                $detail->text('email', L('Email'));
            }
            $detail->tags('roles', L('Roles'))->hookFormatValue(function ($value, AbstractField $field) {
                $item = $field->item();
                /** @var \stdClass $item */
                if (AdminPermission::isFounder($item->id)) {
                    return [L('Admin Founder')];
                }
                return collect($value)->pluck('name')->toArray();
            });
            $detail->display('created_at', L('Created At'));
            $detail->display('updated_at', L('Updated At'));
        });
        $detail->title(L('Admin User'));
        return $detail;
    }
}
