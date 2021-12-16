<?php


namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;

class MemberSelectController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_user')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->display('created_at', '创建时间');
                $builder->image('avatar', '头像');
                $builder->text('username', '用户名');
                $builder->text('email', '邮箱');
                $builder->text('phone', '手机');
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('username', '用户名');
                $filter->like('email', '邮箱');
                $filter->like('phone', '手机');
            })
            ->title('用户管理')
            ->canDelete(false);
    }
}
