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
use ModStart\Repository\Repository;
use ModStart\Support\Concern\HasBuilder;
use ModStart\Support\Concern\HasFields;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Manager\FieldManager;
use stdClass;



class Form implements Renderable
{
    use HasFields,
        HasBuilder,
        HasFluentAttribute,
        HasSort,
        HasCascadeFields,
        HasRepositoryFilter;

    
    private $id;
    
    private $repository;

    
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
        'formClass',
        'treeMaxLevel',
        'treeRootPid',
        'formUrl',
    ];
    
    private $engine = 'basic';
    
    private $mode = 'form';
    
    private $title;
    
    private $showSubmit = true;
    
    private $showReset = true;
    
    private $itemId = null;
    
    private $item;
    
    private $hookSubmitted;
    
    private $hookSaving;
    
    private $hookSaved;
    
    private $hookDeleting;
    
    private $hookDeleted;
    
    private $hookChanged;
    
    private $dataSubmitted;
    
    private $dataForming;
    
    private $dataAdding;
    
    private $dataEditing;
    private $canAdd = true;
    private $canEdit = true;
    private $canDelete = true;
    private $canSort = false;
    private $formClass = '';
    private $treeMaxLevel = 0;
    private $treeRootPid = 0;
    private $formUrl = null;

    
    public function __construct($repository, \Closure $builder = null)
    {
        $this->id = IdUtil::generate('Grid');
        $this->repository = Repository::instance($repository);
        $this->setupFields();
        $this->fieldDefaultRenderMode(FieldRenderMode::FORM);
        $this->setupRepositoryFilter();
        $this->builder($builder);
    }

    public static function make($model, \Closure $builder = null)
    {
        if (class_exists($model) && is_subclass_of($model, Model::class)) {
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
            
            if ($this->treeMaxLevel > 1) {
                $field = FieldManager::make($this, 'select', $this->repository->getTreePidColumn(), L('Parent'));
                $field->optionRepositoryTreeItems($this->repository, $this->treeMaxLevel);
            } else {
                $field = FieldManager::make($this, 'hidden', $this->repository->getTreePidColumn(), L('Parent'));
                $field->value(0);
            }
            $this->prependField($field);
        } else if ($this->engine == FormEngine::TREE_MASS) {
            
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

    
    public function repository()
    {
        return $this->repository;
    }

    private function fieldValidateMessages($fields, $input)
    {
        $failedValidators = [];
        foreach ($fields as $field) {
            if (!$validator = $field->getValidator($input)) {
                continue;
            }
            if (($validator instanceof Validator) && !$validator->passes()) {
                $failedValidators[] = $validator;
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
        if ($validationMessages = $this->fieldValidateMessages($this->editableFields(), $this->dataSubmitted)) {
            $msgsList = array_merge($msgsList, $validationMessages);
        }
        foreach ($msgsList as $column => $msgs) {
            foreach ($msgs as $msg) {
                return Response::generateError($msg);
            }
        }
        return Response::generateSuccess();
    }


    
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

    
    public function add()
    {
        $this->mode(FormMode::ADD);
        $this->build();
        return $this;
    }

    
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

    
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'enablePagination':
            case 'defaultOrder':
            case 'canShow':
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
                return $this;
        }
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        return FieldManager::call($this, $method, $arguments);
    }
}
