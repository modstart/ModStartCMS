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
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\Grid;
use ModStart\ModStart;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Widget\TextDialogRequest;
use Module\Cms\Type\CmsModelFieldType;
use Module\Cms\Util\CmsModelUtil;
use Module\Cms\Util\CmsTemplateUtil;

class ModelController extends Controller
{
    public function fieldEdit($modelId)
    {
        $model = ModelUtil::get('cms_model', $modelId);
        BizException::throwsIfEmpty('模型不存在', $model);
        $id = CRUDUtil::id();
        $record = [
            'title' => '',
            'name' => '',
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
            ModelUtil::decodeRecordBoolean($record, ['isRequired', 'isSearch', 'isList']);
        }
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            $input = InputPackage::buildFromInputJson('data');
            $data = [];
            $data['title'] = $input->getTrimString('title');
            $data['name'] = $input->getTrimString('name');
            $data['fieldType'] = $input->getType('fieldType', CmsModelFieldType::class);
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
            ]));
            $unique = ModelUtil::isFieldUniqueForInsertOrUpdate('cms_model_field', $id, 'name', $data['name'], ['modelId' => $model['id']]);
            BizException::throwsIf('标识重复', !$unique);
            BizException::throwsIf('标识长度范围1-50', strlen($data['name']) < 1 || strlen($data['name']) > 50);
            BizException::throwsIfEmpty('字段类型为空', $data['fieldType']);
            switch ($data['fieldType']) {
                case CmsModelFieldType::RADIO:
                case CmsModelFieldType::SELECT:
                case CmsModelFieldType::CHECKBOX:
                    BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
                    $data['fieldData']['options'] = array_filter(array_map(function ($v) {
                        return trim($v);
                    }, $data['fieldData']['options']));
                    BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
                    break;
            }
            switch ($data['fieldType']) {
                case CmsModelFieldType::TEXT:
                case CmsModelFieldType::TEXTAREA:
                case CmsModelFieldType::RADIO:
                case CmsModelFieldType::SELECT:
                case CmsModelFieldType::CHECKBOX:
                    BizException::throwsIf('字段长度错误', $data['maxLength'] < 1 || $data['maxLength'] > 65535);
                    break;
                case CmsModelFieldType::IMAGE:
                case CmsModelFieldType::FILE:
                    $data['maxLength'] = 200;
                    break;
            }
            $data['fieldData'] = json_encode($data['fieldData']);
            ModelUtil::transactionBegin();
            if ($id) {
                ModelUtil::update('cms_model_field', $id, $data);
                CmsModelUtil::editField($model, $record, $data);
            } else {
                $data['modelId'] = $model['id'];
                $data['sort'] = ModelUtil::sortNext('cms_model_field', ['modelId' => $model['id']]);
                $data = ModelUtil::insert('cms_model_field', $data);
                CmsModelUtil::addField($model, $data);
            }
            ModelUtil::transactionCommit();
            CmsModelUtil::clearCache();
            return Response::generateSuccess();
        }
        return view('module::Cms.View.admin.model.fieldEdit', [
            'record' => $record,
        ]);
    }

    public function fieldDelete($modelId)
    {
        AdminPermission::demoCheck();
        $model = ModelUtil::get('cms_model', $modelId);
        BizException::throwsIfEmpty('模型不存在', $model);
        $id = CRUDUtil::id();
        $record = ModelUtil::get('cms_model_field', $id);
        BizException::throwsIfEmpty('记录不存在', $record);
        ModelUtil::transactionBegin();
        CmsModelUtil::deleteField($model, $record);
        ModelUtil::delete('cms_model_field', $id);
        ModelUtil::transactionCommit();
        CmsModelUtil::clearCache();
        return Response::generateSuccess();
    }

    public function field(AdminDialogPage $page, $modelId)
    {
        $model = ModelUtil::get('cms_model', $modelId);
        BizException::throwsIfEmpty('模型不存在', $model);
        $grid = Grid::make('cms_model_field');
        $grid->repositoryFilter(function (RepositoryFilter $filter) use ($modelId) {
            $filter->where(['modelId' => $modelId]);
        });
        $grid->text('title', '名称');
        $grid->text('name', '标识');
        $grid->title('字段')->dialogSizeSmall();
        $grid->defaultOrder(['sort', 'asc']);
        $grid->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@fieldEdit', ['modelId' => $modelId]));
        $grid->canEdit(true)->urlEdit(action('\\' . __CLASS__ . '@fieldEdit', ['modelId' => $modelId]));
        $grid->canDelete(true)->urlDelete(action('\\' . __CLASS__ . '@fieldDelete', ['modelId' => $modelId]));
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
        $grid->select('listTemplate', '默认列表模板')->options(CmsTemplateUtil::allListTemplateMap());
        $grid->select('detailTemplate', '默认详情模板')->options(CmsTemplateUtil::allDetailTemplateMap());
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
        return $page->pageTitle('模型管理')->body($grid);
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
        ModStart::scriptFile(__DIR__ . '/ModelControllerModel.js');
        $form = Form::make(null);
        $form->text('title', '名称')->required();
        $nameField = $form->text('name', '标识')->required()->ruleRegex('/^[a-z_0-9]+$/')->ruleUnique('cms_model')
            ->help('小写字母、数字、下划线，如 demo_news，创建后不能修改。<a href="javascript:;" data-name-generate><i class="iconfont icon-tools"></i> 自动生成</a>');
        if ($record) {
            $nameField->readonly(true);
        }
        $form->select('listTemplate', '默认列表模板')->options(CmsTemplateUtil::allListTemplateMap());
        $form->select('detailTemplate', '默认详情模板')->options(CmsTemplateUtil::allDetailTemplateMap());
        $form->showSubmit(false)->showReset(false)->formClass('wide');
        $form->item($record)->fillFields();
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->formRequest(function (Form $form) use ($record) {
                $data = $form->dataForming();
                ModelUtil::transactionBegin();
                if ($record) {
                    ModelUtil::update('cms_model', $record['id'], ArrayUtil::keepKeys($data, [
                        'title', 'listTemplate', 'detailTemplate',
                    ]));
                } else {
                    $data = ModelUtil::insert('cms_model', $data);
                    CmsModelUtil::create($data);
                }
                ModelUtil::transactionCommit();
                CmsModelUtil::clearCache();
                return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
            });
        }
        return $page->pageTitle('模型管理')->body($form);
    }

    public function delete()
    {
        AdminPermission::demoCheck();
        $id = CRUDUtil::id();
        $record = ModelUtil::get('cms_model', $id);
        BizException::throwsIfEmpty('记录不存在', $record);
        BizException::throwsIf('有栏目使用，不能删除', ModelUtil::exists('cms_cat', ['modelId' => $record['id']]));
        ModelUtil::transactionBegin();
        CmsModelUtil::drop($record);
        ModelUtil::delete('cms_model', $record['id']);
        ModelUtil::delete('cms_model_field', ['modelId' => $record['id']]);
        ModelUtil::transactionCommit();
        CmsModelUtil::clearCache();
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }

}
