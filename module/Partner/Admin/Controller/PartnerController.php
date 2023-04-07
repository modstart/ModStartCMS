<?php


namespace Module\Partner\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\ButtonDialogRequest;
use Module\Partner\Type\PartnerPosition;
use Module\Partner\Util\PartnerUtil;

class PartnerController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('partner')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->select('position', '位置')->optionType(PartnerPosition::class);
                $builder->text('title', '名称');
                $builder->image('logo', 'Logo');
                $builder->text('link', '链接');
                $builder->switch('enable', '启用')->gridEditable(true)->defaultValue(true);
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('position', '位置')->select(PartnerPosition::class);
                $filter->like('title', L('Title'));
            })
            ->gridOperateAppend(
                ButtonDialogRequest::make('primary', '<i class="iconfont icon-cog"></i> 功能设置', modstart_admin_url('partner/config'))->size('big')
            )
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->title('友情链接')
            ->hookSaved(function (Form $form) {
                PartnerUtil::clearCache();
            });
    }

    public function config(AdminConfigBuilder $builder)
    {
        $builder->useDialog();
        $builder->pageTitle('友情链接设置');
        $builder->text('Partner_Title', '友情链接标题')->defaultValue('合伙伙伴');
        $builder->switch('Partner_LinkDisable', '友情链接不跳转');
        $builder->formClass('wide');
        return $builder->perform();
    }
}
