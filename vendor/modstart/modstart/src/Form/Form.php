<?php


namespace ModStart\Form;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Validator;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Exception\ResultException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Type\SortDirection;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Display;
use ModStart\Field\Select;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Concern\HasCascadeFields;
use ModStart\Form\Type\FormEngine;
use ModStart\Form\Type\FormMode;
use ModStart\Grid\Concerns\HasSort;
use ModStart\Repository\Filter\HasRepositoryFilter;
use ModStart\Repository\Filter\HasScopeFilter;
use ModStart\Repository\Repository;
use ModStart\Support\Concern\HasBuilder;
use ModStart\Support\Concern\HasFields;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Manager\FieldManager;
use stdClass;


/**
 * Class Form.
 *
 * 可用字段
 *
 * @method  Form|mixed mode($value = null)
 * @method  Form|mixed title($value = null)
 * @method  Form|mixed showSubmit($value = null)
 * @method  Form|mixed showReset($value = null)
 *
 * 当前数据ID
 * > add模式：为空
 * > edit模式：当前编辑数据ID
 * > delete模式：当前删除的数据ID集合
 * @method  Form|array|integer|string itemId($value = null)
 *
 * 当前数据
 * > add模式：为空
 * > edit模式：当前编辑数据
 * > delete模式：当前删除的数据集合
 *
 * @method  stdClass|Form|Model|Collection item($value = null)
 *
 * @method  Form|mixed engine($value = null)
 *
 * Hook 数据已提交
 * $value = function (Form $form) { $form->item()->each(function ($item) { }); }
 * @method  Form|mixed hookSubmitted($value = null)
 *
 * Hook 数据正在保存
 * $value = function(Form $form){ return Response::generateError('error'); }
 * @method  Form|mixed hookSaving($value = null)
 *
 * Hook 已保存
 * $value = function (Form $form) { $form->item(); }
 * @method  Form|mixed hookSaved($value = null)
 *
 * Hook 正在删除，会运行在事务中，如果返回标准错误，会阻止删除
 * $value = function (Form $form) { $form->item()->each(function ($item) { }); }
 * @method  Form|mixed hookDeleting($value = null)
 *
 * Hook 已删除
 * $value = function (Form $form) { $form->item()->each(function ($item) { }); }
 * @method  Form|mixed hookDeleted($value = null)
 *
 * Hook 数据已更改（增加、修改、删除、排序）
 * $value = function (Form $form) { RepositoryUtil::makeItems($form->item())->map(function ($item) { });}
 * @method  Form|mixed hookChanged($value = null)
 *
 * @method  Form|mixed dataSubmitted($value = null)
 * @method  Form|mixed dataForming($value = null)
 * @method  Form|mixed dataAdding($value = null)
 * @method  Form|mixed dataEditing($value = null)
 *
 * @method  Form|mixed canAdd($value = null)
 * @method  Form|mixed canEdit($value = null)
 * @method  Form|mixed canDelete($value = null)
 * @method  Form|mixed canCopy($value = null)
 * @method  Form|mixed formClass($value = null)
 * @method  Form|mixed treeMaxLevel($value = null)
 * @method  Form|mixed treeRootPid($value = null)
 * @method  Form|mixed formUrl($value = null)
 * @method  Form|mixed ajax($value = null)
 * @method  Form|mixed formAttr($value = null)
 *
 */
class Form implements Renderable
{
    use HasFields,
        HasBuilder,
        HasFluentAttribute,
        HasSort,
        HasCascadeFields,
        HasScopeFilter,
        HasRepositoryFilter;

    /**
     * @var string
     */
    public $id;
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var string
     */
    private $view = 'modstart::core.form.index';

    protected $fluentAttributes = [
        'engine',
        'builder',
        'mode',
        'title',
        'showSubmit',
        'showReset',
        'itemId',
        'item',
        'hookSubmitted',
        'hookSaving',
        'hookSaved',
        'hookDeleting',
        'hookDeleted',
        'hookChanged',
        'dataSubmitted',
        'dataForming',
        'dataAdding',
        'dataEditing',
        'canAdd',
        'canEdit',
        'canDelete',
        'canSort',
        'canCopy',
        'formClass',
        'treeMaxLevel',
        'treeRootPid',
        'formUrl',
        'ajax',
        'formAttr',
    ];
    /**
     * 运行引擎 @see FormEngine
     * @var string
     */
    private $engine = 'basic';
    /**
     * 表单处理模式 @see FormMode
     * @var string
     */
    private $mode = 'form';
    /**
     * 标题
     * @var string
     */
    private $title;
    /**
     * 是否显示提交
     * @var bool
     */
    private $showSubmit = true;
    /**
     * 是否显示重置
     * @var bool
     */
    private $showReset = true;
    /**
     * 表单编辑、删除的记录ID
     * @var integer|string|array|mixed
     */
    private $itemId = null;
    /**
     * @var Model|stdClass
     */
    private $item;
    /**
     * @var Closure
     */
    private $hookSubmitted;
    /**
     * @var Closure
     */
    private $hookSaving;
    /**
     * @var Closure
     */
    private $hookSaved;
    /**
     * @var Closure
     */
    private $hookDeleting;
    /**
     * @var Closure
     */
    private $hookDeleted;
    /**
     * @var Closure
     */
    private $hookChanged;
    /**
     * @var array
     */
    private $dataSubmitted;
    /**
     * @var array
     */
    private $dataForming;
    /**
     * @var array
     */
    private $dataAdding;
    /**
     * @var array
     */
    private $dataEditing;
    private $canAdd = true;
    private $canEdit = true;
    private $canDelete = true;
    private $canSort = false;
    private $canCopy = false;
    private $formClass = '';
    private $treeMaxLevel = 99;
    private $treeRootPid = 0;
    private $formUrl = null;
    private $ajax = true;
    private $formAttr = '';

    /**
     * Form constructor.
     * @param Model|\Illuminate\Database\Eloquent\Builder|Repository $model
     */
    public function __construct($repository, \Closure $builder = null)
    {
        $this->id = IdUtil::generate('Grid');
        $this->repository = Repository::instance($repository);
        $this->setupFields();
        $this->fieldDefaultRenderMode(FieldRenderMode::FORM);
        $this->setupRepositoryFilter();
        $this->builder($builder);
    }

    public static function make($model = null, \Closure $builder = null)
    {
        if (
            is_object($model)
            ||
            (class_exists($model) && is_subclass_of($model, Model::class))
        ) {
            return new Form($model, $builder);
        }
        return new Form(DynamicModel::make($model), $builder);
    }

    public function asTree($keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository->setKeyName($keyName);
        $this->repository->setTreePidColumn($pidColumn);
        $this->repository->setSortColumn($sortColumn);
        $this->repository->setTreeTitleColumn($titleColumn);
        $this->engine = FormEngine::TREE;
        $this->canSort(true);
        return $this;
    }

    public function asTreeMass($rootPid = 0, $keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository->setKeyName($keyName);
        $this->repository->setTreePidColumn($pidColumn);
        $this->repository->setSortColumn($sortColumn);
        $this->repository->setTreeTitleColumn($titleColumn);
        $this->engine = FormEngine::TREE_MASS;
        $this->canSort(true);
        return $this;
    }

    private function build()
    {
        $this->runBuilder();
        if ($this->engine == FormEngine::TREE) {
            /** @var Select $field */
            if ($this->treeMaxLevel > 1) {
                $field = FieldManager::make($this, 'select', $this->repository->getTreePidColumn(), L('Parent'));
                $field->optionRepositoryTreeItems($this->repository, $this->treeMaxLevel);
            } else {
                $field = FieldManager::make($this, 'hidden', $this->repository->getTreePidColumn(), L('Parent'));
                $field->value(0);
            }
            $this->prependField($field);
        } else if ($this->engine == FormEngine::TREE_MASS) {
            /** @var Display $field */
            $field = FieldManager::make($this, 'display', $this->repository->getTreePidColumn(), L('Parent'));
            $field->addable(true)->editable(true)->listable(false);
            $field->hookRendering(function (AbstractField $field, $item, $index) {
                if (empty($item)) {
                    $pid = InputPackage::buildFromInput()->get('_pid', $this->treeRootPid);
                } else {
                    $pid = $item->{$this->repository->getTreePidColumn()};
                }
                $this->repository()->setArgument('treePid', $pid);
                $ancestors = $this->repository->getTreeAncestorItems();
                $html = [];
                $html[] = '<span class="ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span> ' . L('Root');
                foreach ($ancestors as $ancestor) {
                    $html[] = '<span class="ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span> ' . htmlspecialchars($ancestor->{$this->repository->getTreeTitleColumn()});
                }
                $html[] = '<input type="hidden" name="' . $this->repository->getTreePidColumn() . '" value="' . htmlspecialchars($pid) . '" />';
                return AutoRenderedFieldValue::make(join('', $html));
            });
            $this->prependField($field);
        }
    }

    /**
     * @return Repository|null
     */
    public function repository()
    {
        return $this->repository;
    }

    private function fieldValidateMessages($fields, $input)
    {
        $failedValidators = [];
        /** @var AbstractField $field */
        foreach ($fields as $field) {
            if (!$validator = $field->getValidator($input)) {
                continue;
            }
            if ($validator instanceof Validator) {
                try {
                    if (!$validator->passes()) {
                        $failedValidators[] = $validator;
                    }
                } catch (\Exception $e) {
                    BizException::throws('Form.fieldValidateMessages.Error - ' . json_encode($validator->getRules(), JSON_UNESCAPED_UNICODE));
                }
            }
        }
        $msgs = [];
        foreach ($failedValidators as $validator) {
            foreach ($validator->messages()->getMessages() as $column => $messages) {
                $msgs[$column] = $messages;
            }
        }
        if (empty($msgs)) {
            return false;
        }
        return $msgs;
    }

    private function validateFields($fields, $data)
    {
        $msgsList = [];
        if ($validationMessages = $this->fieldValidateMessages($fields, $data)) {
            $msgsList = array_merge($msgsList, $validationMessages);
        }
        foreach ($msgsList as $column => $msgs) {
            foreach ($msgs as $msg) {
                return Response::generateError($msg);
            }
        }
        return Response::generateSuccess();
    }


    /**
     * 忽略保留字段，如 `id` `created_at` `updated_at`
     *
     * @return void
     */
    private function removeReservedFields()
    {
        $reservedColumns = [
            $this->repository->getKeyName(),
            $this->repository->getCreatedAtColumn(),
            $this->repository->getUpdatedAtColumn(),
        ];
        $reject = function (AbstractField $field) use (&$reservedColumns) {
            return in_array($field->column(), $reservedColumns, true)
                && $field instanceof \ModStart\Field\Display;
        };
        $this->fields = $this->fields()->reject($reject);
    }

    public function hookCall($callback)
    {
        if ($callback instanceof Closure) {
            $ret = call_user_func($callback, $this);
            if (null !== $ret) {
                return $ret;
            }
        }
        return Response::generateSuccess();
    }

    public function isModeForm()
    {
        return $this->mode === FormMode::FORM;
    }

    public function isModeAdd()
    {
        return $this->mode === FormMode::ADD;
    }

    public function isModeEdit()
    {
        return $this->mode === FormMode::EDIT;
    }

    public function isModeDelete()
    {
        return $this->mode === FormMode::DELETE;
    }

    /**
     * @param $callback Closure function(Form $form){ $data = $form->dataForming(); return Response::generateSuccess(); }
     * @param array|null $data
     * @return mixed
     *
     * @example
     *
     * @
     */
    public function formRequest($callback, array $data = null)
    {
        $this->mode(FormMode::FORM);
        $this->build();
        $this->dataSubmitted = $data ? $data : Input::all();
        try {
            ResultException::throwsIfFail($this->validateFields($this->addableFields(), $this->dataSubmitted));
            ResultException::throwsIfFail($this->hookCall($this->hookSubmitted));
            $this->dataForming = [];
            $this->removeReservedFields();
            foreach ($this->addableFields() as $field) {
                if ($field->isLayoutField()) {
                    continue;
                }
                $value = isset($this->dataSubmitted[$field->column()]) ? $this->dataSubmitted[$field->column()] : null;
                $value = $field->prepareInput($value, $this->dataSubmitted);
                $value = $field->serializeValue($value, $field);
                if ($field->hookValueSerialize()) {
                    $value = call_user_func($field->hookValueSerialize(), $value, $field);
                }
                $this->dataForming[$field->column()] = $value;
            }
            ResultException::throwsIfFail($this->hookCall($this->hookSaving));
            $ret = call_user_func($callback, $this);
            if (null !== $ret) {
                if (Response::isError($ret)) {
                    return Response::jsonFromGenerate($ret);
                }
            }
            ResultException::throwsIfFail($this->hookCall($this->hookSaved));
            ResultException::throwsIfFail($this->hookCall($this->hookChanged));
            if (null !== $ret) {
                return Response::jsonFromGenerate($ret);
            }
            if (!empty($this->dataSubmitted['_redirect'])) {
                return Response::json(0, null, null, $this->dataSubmitted['_redirect']);
            }
            return Response::jsonSuccess(L('Save Success'));
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    /**
     * 增加记录
     * @return $this
     */
    public function add()
    {
        $this->mode(FormMode::ADD);
        $isCopy = false;
        if ($this->canCopy()) {
            $copyId = CRUDUtil::copyId();
            if ($copyId) {
                $this->itemId($copyId);
                $this->item($this->repository()->editing($this));
                $this->itemId(0);
                $isCopy = true;
            }
        }
        $this->build();
        if ($isCopy) {
            $this->fillFields();
        }
        return $this;
    }

    /**
     * 增加记录提交
     * @param array|null $data
     * @return mixed
     */
    public function addRequest(array $data = null)
    {
        if (!$this->canAdd) return Response::pagePermissionDenied();
        $this->mode(FormMode::ADD);
        $this->build();
        $this->dataSubmitted = $data ? $data : Input::all();
        try {
            ResultException::throwsIfFail($this->validateFields($this->addableFields(), $this->dataSubmitted));
            ResultException::throwsIfFail($this->hookCall($this->hookSubmitted));
            $this->dataAdding = [];
            $this->removeReservedFields();
            foreach ($this->addableFields() as $field) {
                if ($field->isLayoutField() || $field->isCustomField()) {
                    continue;
                }
                $value = isset($this->dataSubmitted[$field->column()]) ? $this->dataSubmitted[$field->column()] : null;
                $value = $field->prepareInput($value, $this->dataSubmitted);
                $value = $field->serializeValue($value, $field);
                if ($field->hookValueSerialize()) {
                    $value = call_user_func($field->hookValueSerialize(), $value, $field);
                }
                $this->dataAdding[$field->column()] = $value;
            }
            // exit(print_r($this->dataAdding));
            $id = $this->repository->add($this);
            if (!empty($this->dataSubmitted['_redirect'])) {
                return Response::json(0, null, null, $this->dataSubmitted['_redirect']);
            }
            return Response::jsonSuccess(L('Add Success'));
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    /**
     * 编辑记录
     * @return $this
     */
    public function edit($id)
    {
        try {
            $this->mode(FormMode::EDIT);
            $this->itemId($id);
            $this->item($this->repository()->editing($this));
            BizException::throwsIfEmpty(L('Record Not Exists'), $this->item);
            $this->build();
            $this->fillFields();
            return $this;
        } catch (BizException $e) {
            return Response::sendError($e->getMessage());
        }
    }

    /**
     * 编辑记录提交
     *
     * @param $id
     * @return mixed
     * @throws BizException
     * @throws ResultException
     */
    public function editRequest($id, array $data = null)
    {
        if (!$this->canEdit) return Response::pagePermissionDenied();
        $this->dataSubmitted = $data ? $data : Input::all();
        try {
            $this->edit($id);
            ResultException::throwsIfFail($this->validateFields($this->editableFields(), $this->dataSubmitted));
            ResultException::throwsIfFail($this->hookCall($this->hookSubmitted));
            $this->dataEditing = [];
            $this->removeReservedFields();
            $action = isset($this->dataSubmitted['_action']) ? $this->dataSubmitted['_action'] : null;
            if ('itemCellEdit' == $action) {
                $column = isset($this->dataSubmitted['column']) ? $this->dataSubmitted['column'] : null;
                $value = isset($this->dataSubmitted['value']) ? $this->dataSubmitted['value'] : null;
                if ($column) {
                    foreach ($this->editableFields() as $field) {
                        if ($field->isLayoutField() || $field->isCustomField()) {
                            continue;
                        }
                        if ($field->column() == $column) {
                            $this->dataEditing[$field->column()] = $value;
                            break;
                        }
                    }
                }
                BizException::throwsIfEmpty('Data Error', $this->dataEditing);
            } else {
                foreach ($this->editableFields() as $field) {
                    if ($field->isLayoutField() || $field->isCustomField()) {
                        continue;
                    }
                    $value = isset($this->dataSubmitted[$field->column()]) ? $this->dataSubmitted[$field->column()] : null;
                    $value = $field->prepareInput($value, $this->dataSubmitted);
                    $value = $field->serializeValue($value, $field);
                    if ($field->hookValueSerialize()) {
                        $value = call_user_func($field->hookValueSerialize(), $value, $field);
                    }
                    $this->dataEditing[$field->column()] = $value;
                }
            }
            $this->repository()->edit($this);
            if (!empty($this->dataSubmitted['_redirect'])) {
                return Response::json(0, null, null, $this->dataSubmitted['_redirect']);
            }
            return Response::jsonSuccess(L('Edit Success'));
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    /**
     * 删除数据请求
     * @param array $ids
     * @return mixed
     * @throws BizException
     * @throws ResultException
     */
    public function deleteRequest($ids)
    {
        if (!$this->canDelete) return Response::pagePermissionDenied();
        $this->mode(FormMode::DELETE);
        $this->itemId($ids);
        $this->build();
        try {
            $data = $this->repository->deleting($this);
            $this->item($data);
            $this->itemId(collect($data)->map(function ($o) {
                return $o->{$this->repository()->getKeyName()};
            })->toArray());
            $result = $this->repository->delete($this, $data);
            return Response::jsonSuccess(L('Delete Success'));
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    public function sortRequest($ids)
    {
        if (!$this->canSort) return Response::pagePermissionDenied();
        $this->mode(FormMode::SORT);
        $this->itemId($ids);
        $input = InputPackage::buildFromInput();
        $this->repository->setArgument('direction', $input->getType('direction', SortDirection::class));
        $this->build();
        try {
            $result = $this->repository->sortEdit($this);
            ResultException::throwsIfFail($this->hookCall($this->hookChanged));
            return Response::jsonSuccess(L('Operate Success'));
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    /**
     * 渲染增加、编辑页面
     * @return string
     * @throws \Throwable
     */
    public function render()
    {
        $data = [];
        switch ($this->mode) {
            case FormMode::FORM:
                $data['fields'] = $this->addableFields();
                break;
            case FormMode::ADD:
                if (!$this->canAdd) return Response::pagePermissionDenied();
                $data['fields'] = $this->addableFields();
                break;
            case FormMode::EDIT;
                if (!$this->canEdit) return Response::pagePermissionDenied();
                $data['fields'] = $this->editableFields();
                break;
            default:
                return Response::sendError('Form.render mode error : ' . $this->mode);
        }
        $data = array_merge($this->fluentAttributeVariables(), $data);
        return view($this->view, $data)->render();
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return AbstractField|void|$this
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'enablePagination':
            case 'defaultOrder':
            case 'canShow':
            case 'canExport':
            case 'canImport':
            case 'canBatchDelete':
            case 'canMultiSelectItem':
            case 'addBlankPage':
            case 'editBlankPage':
            case 'disableCUD':
            case 'hookItemOperateRendering':
            case 'addDialogSize':
            case 'editDialogSize':
            case 'dialogSizeSmall':
            case 'gridFilter':
            case 'gridOperateAppend':
            case 'bodyAppend':
            case 'operateFixed':
            case 'defaultPageSize':
            case 'pageSizes':
            case 'canBatchSelect':
            case 'batchOperatePrepend':
            case 'gridToolbar';
            case 'pageJumpEnable';
            case 'textEdit':
                return $this;
        }
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        return FieldManager::call($this, $method, $arguments);
    }
}
