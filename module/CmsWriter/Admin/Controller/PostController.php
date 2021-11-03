<?php


namespace Module\CmsWriter\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\GridFilter;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\TextLink;
use Module\Member\Util\MemberCmsUtil;

class PostController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('cms_post')
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->display('memberUserId', '用户')->hookRendering(function (AbstractField $field, $item, $index) {
                    return MemberCmsUtil::showFromId($item->memberUserId);
                });
                $builder->display('title', '标题')->hookRendering(function (AbstractField $field, $item, $index) {
                    return AutoRenderedFieldValue::make(
                        TextLink::primary('<i class="iconfont icon-link"></i>', modstart_web_url('p/' . $item->alias), 'target="_blank"')
                        . ' ' . htmlspecialchars($item->title)
                    );
                });
                $builder->richHtml('contentHtml', '内容')->listable(false);
                $builder->display('created_at', L('Created At'));
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->repositoryFilter(function (RepositoryFilter $filter) {
                $filter->where('memberUserId', '>', '0');
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', '标题');
            })
            ->canEdit(false)->canAdd(false)
            ->title('用户文章');
    }
}
