<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Util\MemberVipUtil;

class MemberVipRightController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '\Module\Member\Admin\Controller\MemberVipSetController@index'
    ];

    /**
     * MemberVipRightController constructor.
     */
    public function __construct()
    {
        $this->useGridDialogPage();
    }

    use HasAdminQuickCRUD;


    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_vip_right')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->checkbox('vipIds', 'VIP等级')->optionModel('member_vip_set', 'id', 'title')->required();
                $builder->text('title', '标题')->required();
                $builder->text('desc', '描述')->required();
                $builder->image('image', '图标')->required();
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->like('title', '名称');
            })
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->canShow(false)
            ->title('权益设置')
            ->addDialogSize(['600px', '95%'])
            ->editDialogSize(['600px', '95%'])
            ->hookSaved(function (Form $form) {
                MemberVipUtil::clearCache();
            });
    }

}
