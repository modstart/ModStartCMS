<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Field\AbstractField;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Cms\Type\CatUrlMode;
use Module\Cms\Type\CmsMode;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsModelUtil;
use Module\Cms\Util\CmsTemplateUtil;

class CatController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('cms_cat')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->text('title', '名称')->required()->width(200);
                $builder->text('url', 'URL')->required()
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        if ($field->renderMode() == FieldRenderMode::GRID) {
                            $url = CatUrlMode::url($item->toArray());
                            return "<a href='$url' target='blank'>$url</a>";
                        }
                        return null;
                    })
                    ->help('字母数字下划线，如，demo 可以通过URL访问 /demo 访问')
                    ->ruleUnique('cms_cat')->ruleRegex('/^[a-zA-Z0-9_\\/]+$/');
                if (modstart_config('CmsUrlMix_Enable', false)) {
                    $builder->text('fullUrl', '[增强]全路径')->listable(false)
                        ->help('如 product/list');
                    $builder->text('pageFullUrl', '[增强]全路径分页')->listable(false)
                        ->help('分页请使用 {page} 占位，如 product/list/{page}，product/list?page={page}');
                }
                $modelField = $builder->select('modelId', '模型')->optionModel('cms_model', 'id', 'title')->required();
                $modelModeMap = CmsModelUtil::listModeMap();
                $modelField->when('in', $modelModeMap[CmsMode::LIST_DETAIL], function ($builder) {
                    $builder->select('listTemplate', '列表模板')->options(CmsTemplateUtil::allListTemplateMap())->required()->listable(false);
                    $builder->select('detailTemplate', '详情模板')->options(CmsTemplateUtil::allDetailTemplateMap())->required()->listable(false);
                });
                $modelField->when('in', $modelModeMap[CmsMode::PAGE], function ($builder) {
                    $builder->select('pageTemplate', '单页模板')->options(CmsTemplateUtil::allPageTemplateMap())->required()->listable(false);
                });
                $modelField->when('in', $modelModeMap[CmsMode::FORM], function ($builder) {
                    $builder->select('formTemplate', '表单模板')->options(CmsTemplateUtil::allFormTemplateMap())->required()->listable(false);
                });
                $builder->text('subTitle', '子标题')->required()->listable(false);
                $builder->image('bannerBg', 'Banner背景')->listable(false);
                $builder->image('icon', '图标')->listable(false);
                $builder->image('cover', '封面')->listable(false);
                $builder->text('seoTitle', 'SEO标题')->listable(false);
                $builder->text('seoDescription', 'SEO描述')->listable(false);
                $builder->textarea('seoKeywords', 'SEO关键词')->listable(false);
                $builder->switch('visitMemberGroupEnable', '用户分组访问限制')->listable(false)
                    ->when('=', true, function ($builder) {
                        /** @var HasFields $builder */
                        $builder->checkbox('visitMemberGroups', '允许访问的用户分组')->optionModel('member_group', 'id', 'title')->listable(false);
                    });
                $builder->switch('visitMemberVipEnable', '用户VIP访问限制')->listable(false)
                    ->when('=', true, function ($builder) {
                        /** @var HasFields $builder */
                        $builder->checkbox('visitMemberVips', '允许访问的用户VIP')->optionModel('member_vip_set', 'id', 'title')->listable(false);
                    });
                if (modstart_config('CmsMemberPost_Enable', false)) {
                    $builder->switch('memberUserPostEnable', '允许用户发布')->optionsYesNo()->listable(false)
                        ->when('=', true, function ($builder) {
                            /** @var HasFields $builder */
                            $builder->switch('postMemberGroupEnable', '用户分组发布限制')->listable(false)
                                ->when('=', true, function ($builder) {
                                    /** @var HasFields $builder */
                                    $builder->checkbox('postMemberGroups', '允许发布的分组')->optionModel('member_group', 'id', 'title')->listable(false);
                                });
                            /** @var HasFields $builder */
                            $builder->switch('postMemberVipEnable', '用户VIP发布限制')->listable(false)
                                ->when('=', true, function ($builder) {
                                    /** @var HasFields $builder */
                                    $builder->checkbox('postMemberVips', '允许发布的VIP')->optionModel('member_vip_set', 'id', 'title')->listable(false);
                                });
                        });
                }
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', L('Title'));
            })
            ->hookChanged(function (Form $form) {
                CmsCatUtil::clearCache();
            })
            ->formClass('wide')
            ->title('栏目管理')
            ->asTree();
    }
}
