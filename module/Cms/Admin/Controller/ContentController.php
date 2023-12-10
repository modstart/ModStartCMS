<?php


namespace Module\Cms\Admin\Controller;


use Carbon\Carbon;
use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\TagUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Tags;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Layout\LayoutGrid;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Support\Manager\FieldManager;
use ModStart\Widget\TextLink;
use Module\Cms\Field\CmsField;
use Module\Cms\Type\CmsContentVerifyStatus;
use Module\Cms\Type\CmsMode;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsModelUtil;
use Module\Cms\Util\CmsTemplateUtil;
use Module\Member\Util\MemberFieldUtil;
use Module\TagManager\Model\TagManager;

class ContentController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    private $model;
    private $modelId;
    private $modelTable;
    private $modelDataTable;

    private function init($modelId)
    {
        AdminPermission::permitCheck('CmsContentManage' . $modelId);
        $this->modelId = $modelId;
        $this->model = CmsModelUtil::get($modelId);
        $this->modelTable = 'cms_content';
        $this->modelDataTable = "cms_m_" . $this->model['name'];
    }

    private function getCatOptions()
    {
        $tree = TreeUtil::modelToTree('cms_cat', [
            'title' => 'title',
            'modelId' => 'modelId',
        ], 'id', 'pid', 'sort', [
            'enable' => true,
        ]);
        $list = TreeUtil::treeToListWithLevel($tree, 'id', 'title', 'pid', 0, ['modelId' => 'modelId']);
        $catOptions = array_build(array_filter($list, function ($v) {
            return $v['modelId'] == $this->modelId;
        }), function ($k, $v) {
            return [$v['id'], str_repeat('|--', $v['level']) . $v['title']];
        });
        return $catOptions;
    }

    public function batchMove(AdminDialogPage $page, $modelId)
    {
        $this->init($modelId);
        $form = Form::make('');
        $ids = array_values(array_unique(CRUDUtil::ids()));
        BizException::throwsIfEmpty('内容ID为空', $ids);
        $contentValidCount = ModelUtil::model($this->modelTable)->whereIn('id', $ids)->where('modelId', $this->modelId)->count();
        BizException::throwsIf('内容ID部分异常', $contentValidCount != count($ids));
        $form->select('catId', '移动到分类')->options($this->getCatOptions());
        $form->showSubmit(false)->showReset(false);
        return $page->body($form)
            ->pageTitle('批量移动')
            ->handleForm($form, function (Form $form) use ($ids) {
                AdminPermission::demoCheck();
                $data = $form->dataForming();
                ModelUtil::model($this->modelTable)->whereIn('id', $ids)->where('modelId', $this->modelId)->update([
                    'catId' => $data['catId'],
                ]);
                return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
            });
    }

    public function index(AdminPage $page, $modelId)
    {
        MemberFieldUtil::register();
        $this->init($modelId);
        $grid = Grid::make($this->modelTable);
        $grid->id('id', 'ID');
        $grid->select('catId', '栏目')->optionModelTree('cms_cat');
        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
            if (modstart_config('CmsMemberPost_Enable', false)) {
                $grid->adminMemberInfo('memberUserId', '用户');
            }
            $grid->text('title', '标题');
            $grid->type('status', '状态')->type(CmsModelContentStatus::class, [
                CmsModelContentStatus::SHOW => 'success',
                CmsModelContentStatus::HIDE => 'muted',
            ]);
            if (modstart_config('CmsMemberPost_Enable', false)) {
                $grid->type('verifyStatus', '审核')->type(CmsContentVerifyStatus::class);
            }
            $listFields = array_filter($this->model['_customFields'], function ($o) {
                return $o['isList'];
            });
            foreach ($listFields as $field) {
                $options = [];
                if (!empty($field['fieldData']['options'])) {
                    $options = array_build($field['fieldData']['options'], function ($k, $v) {
                        return [$v, $v];
                    });
                }
                $grid->display($field['name'], $field['title'])->hookRendering(function (AbstractField $f, $item, $index) use ($field, $options) {
                    $data = ModelUtil::get($this->modelDataTable, $item->id);
                    if (empty($data)) {
                        return AutoRenderedFieldValue::make('');
                    }
                    $viewData = [
                        'value' => $data[$field['name']],
                        'options' => $options,
                    ];
                    $fieldClass = FieldManager::findFieldClass($field['fieldType']);
                    if (!empty($fieldClass)) {
                        /** @var AbstractField $fieldInstance */
                        $fieldInstance = new $fieldClass('_empty_');
                        $viewData['value'] = $fieldInstance->unserializeValue($viewData['value'], $fieldInstance);
                    }
                    $f = CmsField::getByNameOrFail($field['fieldType']);
                    return $f->renderForGrid($viewData);
                });
            }
        } else {
            $customFields = isset($this->model['_customFields']) ? $this->model['_customFields'] : [];
            $grid->display('_content', '内容')->hookRendering(function (AbstractField $field, $item, $index) use ($customFields) {
                $data = CmsContentUtil::getModelData($this->model, $item->id);
                return AutoRenderedFieldValue::makeView('module::Cms.View.admin.content.field.formData', [
                    'item' => $item,
                    'customFields' => $customFields,
                    'data' => $data,
                ]);
            })->width(500);
        }
        $grid->display('updated_at', L('Updated At'));
        if (modstart_config('CmsMemberPost_Enable', false)) {
            $grid->hookItemOperateRendering(function (ItemOperate $itemOperate) {
                /** @var \stdClass $item */
                $item = $itemOperate->item();
                switch ($item->verifyStatus) {
                    case CmsContentVerifyStatus::VERIFYING:
                        $itemOperate->prepend(TextLink::success('审核通过', 'javascript:;', 'data-edit-quick="verifyStatus:' . CmsContentVerifyStatus::VERIFY_PASS . '"'));
                        $itemOperate->prepend(TextLink::danger('审核拒绝', 'javascript:;', 'data-edit-quick="verifyStatus:' . CmsContentVerifyStatus::VERIFY_FAIL . '"'));
                        break;
                }
            });
        }
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where(['modelId' => $this->modelId]);
        });
        $filterFields = array_filter($this->model['_customFields'], function ($o) {
            return $o['isSearch'];
        });
        $tableName = null;
        if (!empty($filterFields)) {
            $tableName = 'cms_m_' . $this->model['name'];
            $grid->gridFilterJoinAdd('left', $tableName, $tableName . '.id', '=', 'cms_content.id');
        }
        $grid->gridFilter(function (GridFilter $filter) use ($filterFields, $tableName) {
            $filter->eq('id', 'ID');
            $filter->like('title', '标题');
            $filter->eq('catId', '栏目')->select($this->getCatOptions());
            $filter->eq('status', '状态')->select(CmsModelContentStatus::class);
            if (!empty($filterFields)) {
                foreach ($filterFields as $filterField) {
                    $filter->like($tableName . '.' . $filterField['name'], $filterField['title']);
                }
            }
        });
        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
            $grid->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@edit', ['modelId' => $this->modelId]));
        } else {
            $grid->canAdd(false);
        }
        $grid->canEdit(true)->urlEdit(action('\\' . __CLASS__ . '@edit', ['modelId' => $this->modelId]));
        $grid->canDelete(true)->urlDelete(action('\\' . __CLASS__ . '@delete', ['modelId' => $this->modelId]));
        $grid->canBatchDelete(true);
        $grid->batchOperatePrepend('<button class="btn" data-batch-dialog-operate="' . modstart_admin_url('cms/content/batch_move/' . $this->modelId) . '"><i class="iconfont icon-right"></i> 批量移动</button>');
        $grid->pageJumpEnable(true);
        $grid->canCopy(true);
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle($this->model['title'])
//            ->row(function (Row $row) {
//                $row->column(12, DashboardItemA::makeIconNumberTitle(
//                    'iconfont icon-details', ModelUtil::count('cms_content', ['modelId' => $this->model['id']]), '总数',
//                    modstart_admin_url('cms/content/' . $this->model['id'])
//                ));
//            })
            ->append($grid);
    }


    public function edit(AdminDialogPage $page, $modelId)
    {
        $input = InputPackage::buildFromInput();
        $this->init($modelId);
        $id = CRUDUtil::id();
        $copyId = CRUDUtil::copyId();
        $record = false;
        if ($id) {
            $record = ModelUtil::get($this->modelTable, $id);
            BizException::throwsIfEmpty('记录不存在', $record);
            $record['_tags'] = TagUtil::string2Array($record['tags']);
            $recordData = ModelUtil::get($this->modelDataTable, $id);
            if (!empty($recordData)) {
                foreach ($recordData as $k => $v) {
                    if (in_array($k, ['id', 'created_at', 'updated_at'])) {
                        continue;
                    }
                    $record[$k] = $v;
                }
            }
            $action = $input->getTrimString('_action');
            if ($action == 'itemCellEdit') {
                AdminPermission::demoCheck();
                $form = Form::make('cms_content');
                $form->radio('verifyStatus', '审核状态')->optionType(CmsContentVerifyStatus::class)->required()->defaultValue(CmsContentVerifyStatus::VERIFY_PASS);
                return $form->editRequest($id);
            }
        } elseif ($copyId) {
            $record = ModelUtil::get($this->modelTable, $copyId);
            if ($record) {
                $record['_tags'] = TagUtil::string2Array($record['tags']);
                $recordData = ModelUtil::get($this->modelDataTable, $copyId);
                if (!empty($recordData)) {
                    foreach ($recordData as $k => $v) {
                        if (in_array($k, ['id', 'created_at', 'updated_at'])) {
                            continue;
                        }
                        $record[$k] = $v;
                    }
                }
                $record['id'] = null;
            }
        }
        $form = Form::make(null);
        $catOptions = $this->getCatOptions();
        $form->layoutGrid(function (LayoutGrid $layout) use ($catOptions) {
            $layout->layoutColumn(
                in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE]) ? 8 : 12,
                function (Form $form) use ($catOptions) {

                    $form->layoutPanel('基本信息', function (Form $form) use ($catOptions) {
                        $form->select('catId', '栏目')->options($catOptions);
                        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
                            $form->text('title', '标题')->required();
                        }
                        if (!empty($this->model['_customFields'])) {
                            $fields = $this->model['_customFields'];
                            foreach ($fields as $field) {
                                $cmsF = CmsField::getByNameOrFail($field['fieldType']);
                                $f = $cmsF->renderForForm($form, $field);
                                if (empty($f)) {
                                    BizException::throws('未知的字段类型' . SerializeUtil::jsonEncode($field));
                                }
                                if ($field['isRequired']) {
                                    $f->required();
                                }
                            }
                        }
                        $form->richHtml('content', '内容');

                        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
                            $form->textarea('summary', '摘要');
                            $form->image('cover', '封面');
                        }

                    });

                    if (
                        in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])
                        || modstart_config('CmsUrlMix_Enable', false)
                    ) {
                        $form->layoutPanel('内容访问', function () use ($form) {
                            if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
                                $form->text('alias', '别名')
                                    ->ruleUnique($this->modelTable)
                                    ->ruleRegex('/^[a-z0-9_]*[a-z][a-z0-9_]*$/')
                                    ->help('数字字母下划线组成，不能是纯数字，可以通过 <code>a/别名</code> 别名访问内容');
                            }
                            if (modstart_config('CmsUrlMix_Enable', false)) {
                                $form->text('fullUrl', '[增强]全路径')->listable(false)
                                    ->help('如 product/view/1.html');
                            }
                        });
                    }

                });
            $layout->layoutColumn(4, function ($form) {

                if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
                    $form->layoutPanel('SEO信息', function (Form $form) {
                        $form->text('seoTitle', 'SEO标题');
                        $form->text('seoDescription', 'SEO描述');
                        $form->textarea('seoKeywords', 'SEO关键词');
                    });
                }

                if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
                    $form->layoutPanel('基本信息', function (Form $form) {

                        $form->datetime('postTime', '发布时间')->required()->help('可以是未来时间，在未来发布')->defaultValue(Carbon::now());
                        $form->radio('status', '状态')->optionType(CmsModelContentStatus::class)->required()->defaultValue(CmsModelContentStatus::SHOW);
                        if (modstart_config('CmsMemberPost_Enable', false)) {
                            $form->radio('verifyStatus', '审核状态')->optionType(CmsContentVerifyStatus::class)->required()->defaultValue(CmsContentVerifyStatus::VERIFY_PASS);
                        }
                        $form->switch('isRecommend', '推荐');
                        $form->switch('isTop', '置顶');
                        $form->tags('tags', '标签')->serializeType(Tags::SERIALIZE_TYPE_COLON_SEPARATED);
                        $form->text('author', '作者');
                        $form->text('source', '来源');
                        $options = array_merge([
                            '' => '默认',
                        ], CmsTemplateUtil::allDetailTemplateMap());
                        $form->select('detailTemplate', '模板')->options($options);
                    });
                }

            });
        });
        $form->item($record)->fillFields();
        $form->showReset(false)->showSubmit(false);
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->formRequest(function (Form $form) use ($record) {
                $data = $form->dataForming();
                $recordValue = ArrayUtil::keepKeys($data, [
                    'catId', 'title', 'alias', 'title', 'summary', 'cover', 'postTime',
                    'status', 'isRecommend', 'isTop', 'tags', 'author', 'source',
                    'seoTitle', 'seoDescription', 'seoKeywords',
                    'detailTemplate',
                ]);
                if (modstart_config('CmsUrlMix_Enable', false)) {
                    $recordValue['fullUrl'] = (empty($data['fullUrl']) ? null : $data['fullUrl']);
                }
                if (empty($recordValue['verifyStatus'])) {
                    $recordValue['verifyStatus'] = CmsContentVerifyStatus::VERIFY_PASS;
                }
                if (empty($recordValue['alias'])) {
                    $recordValue['alias'] = null;
                }
                $recordDataValue = ArrayUtil::keepKeys($data, [
                    'content',
                ]);
                if (!empty($this->model['_customFields'])) {
                    $fields = $this->model['_customFields'];
                    foreach ($fields as $field) {
                        $recordDataValue[$field['name']] = isset($data[$field['name']]) ? $data[$field['name']] : null;
                    }
                }
                ModelUtil::transactionBegin();
                if (!empty($record['id'])) {
                    ModelUtil::update($this->modelTable, $record['id'], $recordValue);
                    $recordDataValue['updated_at'] = Carbon::now();
                    if (ModelUtil::update($this->modelDataTable, $record['id'], $recordDataValue) < 1) {
                        ModelUtil::insert($this->modelDataTable, array_merge($recordDataValue, [
                            'id' => $record['id']
                        ]));
                    }
                    if (modstart_module_enabled('TagManager')) {
                        TagManager::updateTags('cms', $record['_tags'], $recordValue['tags']);
                    }
                } else {
                    $recordValue['modelId'] = $this->model['id'];
                    $recordValue = ModelUtil::insert($this->modelTable, $recordValue);
                    $recordDataValue['id'] = $recordValue['id'];
                    ModelUtil::insert($this->modelDataTable, $recordDataValue);
                    if (modstart_module_enabled('TagManager')) {
                        TagManager::putTags('cms', $recordValue['tags']);
                    }
                }
                ModelUtil::transactionCommit();
                return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
            });
        }
        return $page->pageTitle($this->model['title'] . '编辑')->body($form);
    }

    public function delete($modelId)
    {
        AdminPermission::demoCheck();
        $this->init($modelId);
        $ids = CRUDUtil::ids();
        foreach ($ids as $id) {
            $record = ModelUtil::get($this->modelTable, $id);
            $record['_tags'] = TagUtil::string2Array($record['tags']);
            BizException::throwsIfEmpty('记录不存在', $id);
            ModelUtil::transactionBegin();
            ModelUtil::delete($this->modelTable, $record['id']);
            ModelUtil::delete($this->modelDataTable, $record['id']);
            if (modstart_module_enabled('TagManager')) {
                TagManager::deleteTags('cms', $record['_tags']);
            }
            ModelUtil::transactionCommit();
        }
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }
}
