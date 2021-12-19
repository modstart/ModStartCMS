<?php


namespace Module\Cms\Admin\Controller;


use Carbon\Carbon;
use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Tags;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Repository\Filter\RepositoryFilter;
use Module\Cms\Type\CmsMode;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Type\CmsModelFieldType;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsModelUtil;

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

    public function index(AdminPage $page, $modelId)
    {
        $this->init($modelId);
        $grid = Grid::make($this->modelTable);
        $grid->id('id', 'ID');
        $grid->select('catId', '栏目')->optionModelTree('cms_cat', 'id', 'title');
        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
            $grid->text('title', '标题');
            $grid->type('status', '状态')->type(CmsModelContentStatus::class, [
                CmsModelContentStatus::SHOW => 'success',
                CmsModelContentStatus::HIDE => 'muted',
            ]);
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
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where(['modelId' => $this->modelId]);
        });
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('id', 'ID');
            $filter->like('title', '标题');
            $filter->eq('status', '状态')->select(CmsModelContentStatus::class);
        });
        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
            $grid->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@edit', ['modelId' => $this->modelId]));
        } else {
            $grid->canAdd(false);
        }
        $grid->canEdit(true)->urlEdit(action('\\' . __CLASS__ . '@edit', ['modelId' => $this->modelId]));
        $grid->canDelete(true)->urlDelete(action('\\' . __CLASS__ . '@delete', ['modelId' => $this->modelId]));
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle($this->model['title'] . '管理')
//            ->row(function (Row $row) {
//                $row->column(3, DashboardItemA::makeIconNumberTitle(
//                    'iconfont icon-details', ModelUtil::count('cms_content', ['modelId' => $this->model['id']]), '总数',
//                    modstart_admin_url('cms/content/' . $this->model['id'])
//                ));
//            })
            ->append($grid);
    }

    public function edit(AdminDialogPage $page, $modelId)
    {
        $this->init($modelId);
        $id = CRUDUtil::id();
        $record = false;
        if ($id) {
            $record = ModelUtil::get($this->modelTable, $id);
            BizException::throwsIfEmpty('记录不存在', $id);
            $recordData = ModelUtil::get($this->modelDataTable, $id);
            if (!empty($recordData)) {
                foreach ($recordData as $k => $v) {
                    if (in_array($k, ['id', 'created_at', 'updated_at'])) {
                        continue;
                    }
                    $record[$k] = $v;
                }
            }
        }
        $form = Form::make(null);
        $tree = TreeUtil::modelToTree('cms_cat', [
            'title' => 'title',
            'modelId' => 'modelId',
        ]);
        $list = TreeUtil::treeToListWithLevel($tree, 'id', 'title', 'pid', 0, ['modelId' => 'modelId']);
        $options = array_build(array_filter($list, function ($v) {
            return $v['modelId'] == $this->modelId;
        }), function ($k, $v) {
            return [$v['id'], str_repeat('|--', $v['level']) . $v['title']];
        });
        $form->select('catId', '栏目')->options($options);
        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
            $form->text('title', '标题')->required();
        }
        if (!empty($this->model['_customFields'])) {
            $fields = $this->model['_customFields'];
            foreach ($fields as $field) {
                $f = null;
                $options = [];
                if (!empty($field['fieldData']['options'])) {
                    $options = array_build($field['fieldData']['options'], function ($k, $v) {
                        return [$v, $v];
                    });
                }
                switch ($field['fieldType']) {
                    case CmsModelFieldType::TEXT:
                        $f = $form->text($field['name'], $field['title']);
                        break;
                    case CmsModelFieldType::TEXTAREA:
                        $f = $form->textarea($field['name'], $field['title']);
                        break;
                    case CmsModelFieldType::RADIO:
                        $f = $form->radio($field['name'], $field['title'])->options($options);
                        break;
                    case CmsModelFieldType::SELECT:
                        $f = $form->select($field['name'], $field['title'])->options($options);
                        break;
                    case CmsModelFieldType::CHECKBOX:
                        $f = $form->checkbox($field['name'], $field['title'])->options($options);
                        break;
                    case CmsModelFieldType::IMAGE:
                        $f = $form->image($field['name'], $field['title']);
                        break;
                    case CmsModelFieldType::FILE:
                        $f = $form->file($field['name'], $field['title']);
                        break;
                    case CmsModelFieldType::DATE:
                        $f = $form->date($field['name'], $field['title']);
                        break;
                    case CmsModelFieldType::DATETIME:
                        $f = $form->datetime($field['name'], $field['title']);
                        break;
                    case CmsModelFieldType::RICH_TEXT:
                        $f = $form->richHtml($field['name'], $field['title']);
                        break;
                }
                if (empty($f)) {
                    BizException::throws('未知的字段类型' . json_encode($field, JSON_UNESCAPED_UNICODE));
                }
                if ($field['isRequired']) {
                    $f->required();
                }
            }
        }
        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
            $form->text('alias', '别名')
                ->ruleUnique($this->modelTable)
                ->ruleRegex('/^[a-z0-9_]*[a-z][a-z0-9_]*$/')
                ->help('数字字母下划线组成，不能是纯数字，可以通过 <code>a/别名</code> 别名访问内容');
        }
        $form->richHtml('content', '内容');
        if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
            $form->textarea('summary', '摘要');
            $form->image('cover', '封面');
            $form->datetime('postTime', '发布时间')->required()->help('可以是未来时间，在未来发布')->defaultValue(Carbon::now());
            $form->radio('status', '状态')->optionType(CmsModelContentStatus::class)->required()->defaultValue(CmsModelContentStatus::SHOW);
            $form->switch('isRecommend', '推荐');
            $form->switch('isTop', '置顶');
            $form->tags('tags', '标签')->serializeType(Tags::SERIALIZE_TYPE_COLON_SEPARATED);
            $form->text('author', '作者');
            $form->text('source', '来源');
            $form->text('seoTitle', 'SEO标题');
            $form->text('seoDescription', 'SEO描述');
            $form->textarea('seoKeywords', 'SEO关键词');
        }
        $form->item($record)->fillFields();
        $form->showReset(false)->showSubmit(false);
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->formRequest(function (Form $form) use ($record) {
                $data = $form->dataForming();
                $recordValue = ArrayUtil::keepKeys($data, [
                    'catId', 'title', 'alias', 'title', 'summary', 'cover', 'postTime',
                    'status', 'isRecommend', 'isTop', 'tags', 'author', 'source',
                ]);
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
                if ($record) {
                    ModelUtil::update($this->modelTable, $record['id'], $recordValue);
                    ModelUtil::update($this->modelDataTable, $record['id'], $recordDataValue);
                } else {
                    $recordValue['modelId'] = $this->model['id'];
                    $recordValue = ModelUtil::insert($this->modelTable, $recordValue);
                    $recordDataValue['id'] = $recordValue['id'];
                    ModelUtil::insert($this->modelDataTable, $recordDataValue);
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
        $id = CRUDUtil::id();
        $record = ModelUtil::get($this->modelTable, $id);
        BizException::throwsIfEmpty('记录不存在', $id);
        ModelUtil::transactionBegin();
        ModelUtil::delete($this->modelTable, $record['id']);
        ModelUtil::delete($this->modelDataTable, $record['id']);
        ModelUtil::transactionCommit();
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }
}
