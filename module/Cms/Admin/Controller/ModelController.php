<?php


namespace Module\Cms\Admin\Controller;


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
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\Grid;
use ModStart\ModStart;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Widget\TextDialogRequest;
use Module\Cms\Field\CmsField;
use Module\Cms\Type\CmsMode;
use Module\Cms\Util\CmsModelUtil;
use Module\Cms\Util\CmsTemplateUtil;

class ModelController extends Controller
{
    public function fieldEdit($modelId)
    {
        $model = ModelUtil::get('cms_model', $modelId);
        BizException::throwsIfEmpty('模型不存在[id=' . $modelId . ']', $model);
        $id = CRUDUtil::id();
        $record = [
            'title' => '',
            'name' => '',
            'enable' => true,
            'fieldType' => 'text',
            'fieldData' => new \stdClass(),
            'isRequired' => true,
            'isSearch' => true,
            'isList' => true,
            'placeholder' => '',
            'maxLength' => 100,
        ];
        if ($id) {
            $record = ModelUtil::get('cms_model_field', $id);
            BizException::throwsIfEmpty('记录不存在', $record);
            ModelUtil::decodeRecordJson($record, ['fieldData']);
            ModelUtil::decodeRecordBoolean($record, ['enable', 'isRequired', 'isSearch', 'isList']);
        }
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            $input = InputPackage::buildFromInputJson('data');
            $data = [];
            $data['title'] = $input->getTrimString('title');
            $data['name'] = $input->getTrimString('name');
            $data['enable'] = $input->getBoolean('enable');
            $data['fieldType'] = $input->getTrimString('fieldType');
            $data['fieldData'] = $input->getArray('fieldData');
            $data['isRequired'] = $input->getBoolean('isRequired');
            $data['isSearch'] = $input->getBoolean('isSearch');
            $data['isList'] = $input->getBoolean('isList');
            $data['placeholder'] = $input->getTrimString('placeholder');
            $data['maxLength'] = $input->getInteger('maxLength');
            BizException::throwsIfEmpty('标题为空', $data['title']);
            BizException::throwsIfEmpty('标识为空', $data['name']);
            BizException::throwsIf('标识格式不正确', !preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $data['name']));
            BizException::throwsIf('标识不能为系统关键字段', in_array($data['name'], [
                'id', 'created_at', 'updated_at', 'content',
                'catId', 'modelId', 'alias', 'title', 'summary', 'cover',
                'seoTitle', 'seoDescription', 'seoKeywords',
                'postTime', 'wordCount', 'viewCount',
                'status', 'commentCount', 'likeCount',
                'isRecommend', 'isTop', 'tags',
                'author', 'source', 'memberUserId', 'verifyStatus'
            ]));
            $unique = ModelUtil::isFieldUniqueForInsertOrUpdate('cms_model_field', $id, 'name', $data['name'], ['modelId' => $model['id']]);
            BizException::throwsIf('标识重复', !$unique);
            BizException::throwsIf('标识长度范围1-50', strlen($data['name']) < 1 || strlen($data['name']) > 50);
            BizException::throwsIfEmpty('字段类型为空', $data['fieldType']);

            $f = CmsField::getByNameOrFail($data['fieldType']);
            $data = $f->prepareDataOrFail($data);
            $data['fieldData'] = json_encode($data['fieldData'], JSON_UNESCAPED_UNICODE);
            if ($id) {
                ModelUtil::update('cms_model_field', $id, $data);
                $data['fieldData'] = json_decode($data['fieldData'], true);
                CmsModelUtil::editField($model, $record, $data);
            } else {
                $data['modelId'] = $model['id'];
                $data['sort'] = ModelUtil::sortNext('cms_model_field', ['modelId' => $model['id']]);
                $data = ModelUtil::insert('cms_model_field', $data);
                $data['fieldData'] = json_decode($data['fieldData'], true);
                CmsModelUtil::addField($model, $data);
            }
            CmsModelUtil::clearCache();
            return Response::generateSuccess();
        }
        return view('module::Cms.View.admin.model.fieldEdit', [
            'record' => $record,
        ]);
    }

    public function fieldSort($modelId)
    {
        AdminPermission::demoCheck();
        $model = ModelUtil::get('cms_model', $modelId);
        BizException::throwsIfEmpty('模型不存在[id=' . $modelId . ']', $model);
        $id = CRUDUtil::id();
        $record = ModelUtil::get('cms_model_field', $id);
        BizException::throwsIfEmpty('记录不存在', $record);
        $form = Form::make('cms_model_field');
        $form->repositoryFilter(function (RepositoryFilter $filter) use ($modelId) {
            $filter->where(['modelId' => $modelId]);
        });
        $form->canSort(true);
        return $form->sortRequest($id);
    }

    public function fieldDelete($modelId)
    {
        AdminPermission::demoCheck();
        $model = ModelUtil::get('cms_model', $modelId);
        BizException::throwsIfEmpty('模型不存在[id=' . $modelId . ']', $model);
        $id = CRUDUtil::id();
        $record = ModelUtil::get('cms_model_field', $id);
        BizException::throwsIfEmpty('记录不存在', $record);
        CmsModelUtil::deleteField($model, $record);
        ModelUtil::transactionBegin();
        ModelUtil::delete('cms_model_field', $id);
        ModelUtil::transactionCommit();
        CmsModelUtil::clearCache();
        return Response::generateSuccess();
    }

    public function field(AdminDialogPage $page, $modelId)
    {
        $model = ModelUtil::get('cms_model', $modelId);
        BizException::throwsIfEmpty('模型不存在[id=' . $modelId . ']', $model);
        $grid = Grid::make('cms_model_field');
        $grid->repositoryFilter(function (RepositoryFilter $filter) use ($modelId) {
            $filter->where(['modelId' => $modelId]);
        });
        $grid->text('title', '名称');
        $grid->text('name', '标识');
        $grid->switch('enable', '启用')->optionsYesNo()->required();
        $grid->title('字段')->dialogSizeSmall();
        $grid->defaultOrder(['sort', 'asc']);
        $grid->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@fieldEdit', ['modelId' => $modelId]));
        $grid->canEdit(true)->urlEdit(action('\\' . __CLASS__ . '@fieldEdit', ['modelId' => $modelId]));
        $grid->canDelete(true)->urlDelete(action('\\' . __CLASS__ . '@fieldDelete', ['modelId' => $modelId]));
        $grid->canSort(true)->urlSort(action('\\' . __CLASS__ . '@fieldSort', ['modelId' => $modelId]));
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle("模型$model[title]($model[name])字段管理")->body($grid);
    }

    public function index(AdminPage $page)
    {
        $grid = Grid::make('cms_model');
        $grid->text('title', '名称');
        $grid->text('name', '标识');
        $grid->switch('enable', '启用')->optionsYesNo()->listable(true);
        $grid->type('mode', '类型')->type(CmsMode::class);
        $grid->display('_mode', '模式')->hookRendering(function (AbstractField $field, $item, $index) {
            return AutoRenderedFieldValue::makeView('module::Cms.View.admin.model.field.mode', [
                'item' => $item,
            ]);
        });
        $grid->title('模型')->dialogSizeSmall();
        $grid->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@edit'));
        $grid->canEdit(true)->urlEdit(action('\\' . __CLASS__ . '@edit'));
        $grid->canDelete(true)->urlDelete(action('\\' . __CLASS__ . '@delete'));
        $grid->hookItemOperateRendering(function (ItemOperate $itemOperate) {
            $item = $itemOperate->item();
            $fieldDialog = new TextDialogRequest();
            $fieldDialog->type('primary');
            $fieldDialog->text('<i class="iconfont icon-tools"></i> 字段管理');
            $fieldDialog->url(action('\\' . __CLASS__ . '@field', ['modelId' => $item->id]));
            $fieldDialog->width('90%');
            $fieldDialog->height('90%');
            $itemOperate->prepend($fieldDialog);
        });
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle('内容模型')->body($grid);
    }

    public function edit(AdminDialogPage $page)
    {
        $id = CRUDUtil::id();
        $record = false;
        if ($id) {
            $record = ModelUtil::get('cms_model', $id);
            BizException::throwsIfEmpty('记录不存在', $record);
        }
        ModStart::js('vendor/Cms/entry/pinyin.js');
        ModStart::scriptFile('module/Cms/Admin/Controller/ModelControllerModel.js');
        $form = Form::make(null);
        $form->text('title', '名称')->required();
        $nameField = $form->text('name', '标识')->required()->ruleRegex('/^[a-z_0-9]+$/')->ruleUnique('cms_model')
            ->help('小写字母、数字、下划线，如 demo_news，创建后不能修改。<a href="javascript:;" data-name-generate><i class="iconfont icon-tools"></i> 自动生成</a>');
        if ($record) {
            $nameField->readonly(true);
        }
        $form->switch('enable', '启用')->optionsYesNo()->required();
        $form->radio('mode', '显示模式')->optionType(CmsMode::class)
            ->when('=', CmsMode::LIST_DETAIL, function (Form $form) {
                $form->select('listTemplate', '默认列表模板')->options(CmsTemplateUtil::allListTemplateMap());
                $form->select('detailTemplate', '默认详情模板')->options(CmsTemplateUtil::allDetailTemplateMap());
            })
            ->when('=', CmsMode::PAGE, function (Form $form) {
                $form->select('pageTemplate', '默认单页模板')->options(CmsTemplateUtil::allPageTemplateMap());
            })
            ->when('=', CmsMode::FORM, function (Form $form) {
                $form->select('formTemplate', '默认表单模板')->options(CmsTemplateUtil::allFormTemplateMap());
            })
            ->defaultValue(CmsMode::LIST_DETAIL)->required();
        $form->showSubmit(false)->showReset(false)->formClass('wide');
        $form->item($record)->fillFields();
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->formRequest(function (Form $form) use ($record) {
                $data = $form->dataForming();
                if ($record) {
                    ModelUtil::update('cms_model', $record['id'], ArrayUtil::keepKeys($data, [
                        'title', 'enable', 'form', 'mode', 'listTemplate', 'detailTemplate', 'pageTemplate',
                    ]));
                } else {
                    $data = ModelUtil::insert('cms_model', $data);
                    CmsModelUtil::create($data);
                }
                CmsModelUtil::clearCache();
                return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
            });
        }
        return $page->pageTitle('内容模型')->body($form);
    }

    public function delete()
    {
        AdminPermission::demoCheck();
        $id = CRUDUtil::id();
        $record = ModelUtil::get('cms_model', $id);
        BizException::throwsIfEmpty('记录不存在', $record);
        BizException::throwsIf('有栏目使用，不能删除', ModelUtil::exists('cms_cat', ['modelId' => $record['id']]));
        BizException::throwsIf('该模型有数据，不能删除', ModelUtil::exists('cms_content', ['modelId' => $record['id']]));
        CmsModelUtil::drop($record);
        ModelUtil::transactionBegin();
        ModelUtil::delete('cms_model', $record['id']);
        ModelUtil::delete('cms_model_field', ['modelId' => $record['id']]);
        ModelUtil::transactionCommit();
        CmsModelUtil::clearCache();
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }

}
