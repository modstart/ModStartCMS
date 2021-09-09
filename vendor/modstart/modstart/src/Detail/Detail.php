<?php


namespace ModStart\Detail;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\IdUtil;
use ModStart\Detail\Type\DetailEngine;
use ModStart\Field\AbstractField;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Concern\HasCascadeFields;
use ModStart\Form\Form;
use ModStart\Repository\Filter\HasRepositoryFilter;
use ModStart\Repository\Repository;
use ModStart\Support\Concern\HasBuilder;
use ModStart\Support\Concern\HasFields;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Manager\FieldManager;


class Detail implements Renderable
{
    use HasFields,
        HasBuilder,
        HasFluentAttribute,
        HasCascadeFields,
        HasRepositoryFilter;

    
    private $id;
    
    private $repository;
    
    private $view = 'modstart::core.detail.index';

    private $fluentAttributes = [
        'engine',
        'title',
        'itemId',
        'item',
        'formClass',
    ];
    
    private $engine = 'basic';
    private $title;
    
    private $itemId = null;
    
    private $item;
    private $formClass = '';

    
    public function __construct($repository, \Closure $builder = null)
    {
        $this->id = IdUtil::generate('Grid');
        $this->repository = Repository::instance($repository);
        $this->setupFields();
        $this->fieldDefaultRenderMode(FieldRenderMode::DETAIL);
        $this->setupRepositoryFilter();
        $this->builder($builder);
    }

    public static function make($model, \Closure $builder = null)
    {
        if (class_exists($model) && is_subclass_of($model, \Illuminate\Database\Eloquent\Model::class)) {
            return new Detail($model, $builder);
        }
        return new Detail(DynamicModel::make($model), $builder);
    }

    public function asTree($keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository->setKeyName($keyName);
        $this->repository->setTreePidColumn($pidColumn);
        $this->repository->setSortColumn($sortColumn);
        $this->repository->setTreeTitleColumn($titleColumn);
        $this->engine = DetailEngine::TREE;
        return $this;
    }

    public function asTreeMass($rootPid = 0, $keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository->setKeyName($keyName);
        $this->repository->setTreePidColumn($pidColumn);
        $this->repository->setSortColumn($sortColumn);
        $this->repository->setTreeTitleColumn($titleColumn);
        $this->engine = DetailEngine::TREE_MASS;
        return $this;
    }

    
    public function repository()
    {
        return $this->repository;
    }

    private function build()
    {
        $this->runBuilder();
    }

    
    public function show($id)
    {
        $this->itemId($id);
        $this->item($this->repository()->show($this));
        BizException::throwsIfEmpty(L('Record Not Exists'), $this->item);
        $this->build();
        $this->fillFields();
        return $this;
    }

    public function render()
    {
        $data = [];
        $data['fields'] = $this->showableFields();
        $data = array_merge($this->fluentAttributeVariables(), $data);
        return view($this->view, $data)->render();
    }

    
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'enablePagination':
            case 'defaultOrder':
            case 'canAdd':
            case 'canEdit':
            case 'canDelete':
            case 'canSort':
            case 'canShow':
            case 'canBatchDelete':
            case 'treeMaxLevel':
            case 'hookSaved':
            case 'hookChanged':
            case 'hookDeleted':
            case 'hookItemOperateRendering':
            case 'addBlankPage':
            case 'editBlankPage':
            case 'disableCUD':
            case 'addDialogSize':
            case 'editDialogSize':
            case 'dialogSizeSmall':
            case 'gridFilter':
            case 'canMultiSelectItem':
                return $this;
        }
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        return FieldManager::call($this, $method, $arguments);
    }
}
