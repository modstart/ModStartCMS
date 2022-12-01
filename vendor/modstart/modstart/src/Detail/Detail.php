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
use ModStart\Repository\Filter\HasScopeFilter;
use ModStart\Repository\Repository;
use ModStart\Support\Concern\HasBuilder;
use ModStart\Support\Concern\HasFields;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Manager\FieldManager;

/**
 * Class Detail
 * @package ModStart\Detail
 *
 * @method  Detail|mixed engine($value = null)
 * @method  Detail|mixed title($value = null)
 * @method  Detail|mixed formClass($value = null)
 * @method  Detail|array|integer|string itemId($value = null)
 * @method  Detail|\Illuminate\Database\Eloquent\Model|\stdClass item($value = null)
 *
 */
class Detail implements Renderable
{
    use HasFields,
        HasBuilder,
        HasFluentAttribute,
        HasCascadeFields,
        HasScopeFilter,
        HasRepositoryFilter;

    /**
     * @var string
     */
    private $id;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var string
     */
    private $view = 'modstart::core.detail.index';

    private $fluentAttributes = [
        'engine',
        'title',
        'itemId',
        'item',
        'formClass',
    ];
    /**
     * 运行引擎 @see DetailEngine
     * @var string
     */
    private $engine = 'basic';
    private $title;
    /**
     * 表单编辑、删除的记录ID
     * @var integer|string|array|mixed
     */
    private $itemId = null;
    /**
     * 当前展示详情记录
     * @var Model|\stdClass
     */
    private $item;
    private $formClass = '';

    /**
     * Form constructor.
     * @param Model|\Illuminate\Database\Eloquent\Builder|Repository $model
     */
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
        if (
            is_object($model)
            ||
            (class_exists($model) && is_subclass_of($model, \Illuminate\Database\Eloquent\Model::class))
        ) {
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

    /**
     * @return Repository|null
     */
    public function repository()
    {
        return $this->repository;
    }

    private function build()
    {
        $this->runBuilder();
    }

    /**
     * 显示记录
     * @return $this
     * @throws BizException
     */
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
            case 'canAdd':
            case 'canEdit':
            case 'canDelete':
            case 'canSort':
            case 'canShow':
            case 'canExport':
            case 'canImport':
            case 'canCopy':
            case 'canBatchDelete':
            case 'treeMaxLevel':
            case 'hookSaving':
            case 'hookSaved':
            case 'hookChanged':
            case 'hookDeleting':
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
            case 'gridOperateAppend':
            case 'bodyAppend':
            case 'operateFixed':
            case 'defaultPageSize':
            case 'pageSizes':
            case 'canBatchSelect':
            case 'batchOperatePrepend':
            case 'gridToolbar':
            case 'pageJumpEnable':
            case 'textEdit':
                return $this;
        }
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        return FieldManager::call($this, $method, $arguments);
    }
}
