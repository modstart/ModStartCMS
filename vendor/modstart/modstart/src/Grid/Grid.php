<?php


namespace ModStart\Grid;

use Closure;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\IdUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\Concerns\HasGridFilter;
use ModStart\Grid\Concerns\HasItemOperate;
use ModStart\Grid\Concerns\HasPaginator;
use ModStart\Grid\Concerns\HasSort;
use ModStart\Grid\Type\GridEngine;
use ModStart\Repository\Filter\HasRepositoryFilter;
use ModStart\Support\Concern\HasBuilder;
use ModStart\Support\Concern\HasFields;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Concern\HasSetting;
use ModStart\Support\Manager\FieldManager;


class Grid
{
    use HasBuilder,
        HasFields,
        HasFluentAttribute,
        HasGridFilter,
        HasItemOperate,
        HasPaginator,
        HasSort,
        HasRepositoryFilter;

    
    private $id;
    
    private $model;

    protected $fluentAttributes = [
        'view',
        'engine',
        'title',
        'titleAdd',
        'titleEdit',
        'titleShow',
        'canAdd',
        'canEdit',
        'canDelete',
        'canShow',
        'canExport',
        'canMultiSelectItem',
        'canSingleSelectItem',
        'canBatchDelete',
        'canBatchSelect',
        'canSort',
        'urlAdd',
        'urlEdit',
        'urlDelete',
        'urlShow',
        'urlExport',
        'urlSort',
        'addDialogSize',
        'editDialogSize',
        'showDialogSize',
        'addBlankPage',
        'editBlankPage',
        'enablePagination',
        'defaultOrder',
        'treeMaxLevel',
        'treeRootPid',
        'batchOperatePrepend',
        'hookPrepareItems',
        'gridRowCols',
    ];
    
    private $engine = 'basic';
    private $title;
    private $titleAdd;
    private $titleEdit;
    private $titleShow;
    private $canAdd = true;
    private $canEdit = true;
    private $canDelete = true;
    private $canShow = true;
    private $canExport = false;
    private $canMultiSelectItem = false;
    private $canSingleSelectItem = false;
    private $canBatchDelete = false;
    private $canBatchSelect = false;
    private $canSort = false;
    private $urlAdd;
    private $urlEdit;
    private $urlDelete;
    private $urlShow;
    private $urlExport;
    private $urlSort;
    private $addDialogSize = ['95%', '95%'];
    private $editDialogSize = ['95%', '95%'];
    private $showDialogSize = ['95%', '95%'];
    private $addBlankPage = false;
    private $editBlankPage = false;
    private $enablePagination = true;
    private $defaultOrder = [];
    private $treeMaxLevel = 0;
    private $treeRootPid = 0;
    private $batchOperatePrepend = '';
    
    private $gridRowCols = null;

    
    private $hookPrepareItems = null;

    private $isBuilt = false;

    private $view = 'modstart::core.grid.index';

    
    public function __construct($repository = null, \Closure $builder = null)
    {
        $this->id = IdUtil::generate('Grid');
        $this->model = new Model($this, $repository);
        $this->setupFields();
        $this->fieldDefaultRenderMode(FieldRenderMode::GRID);
        $this->setupRepositoryFilter();
        $this->setupGridFilter();
        $this->setupItemOperate();
        $this->builder($builder);
    }

    
    public static function make($model, \Closure $builder = null)
    {
        if (class_exists($model) && is_subclass_of($model, \Illuminate\Database\Eloquent\Model::class)) {
            return new Grid($model, $builder);
        }
        return new Grid(DynamicModel::make($model), $builder);
    }

    
    public function useSimple($htmlHookRending)
    {
        $this->view = 'modstart::core.grid.simple';
        $this->disableItemOperate();
        $this->display('html', 'html')->hookRendering($htmlHookRending);
        return $this;
    }

    public function asTree($keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository()->setKeyName($keyName);
        $this->repository()->setTreePidColumn($pidColumn);
        $this->repository()->setSortColumn($sortColumn);
        $this->repository()->setTreeTitleColumn($titleColumn);
        $this->engine = GridEngine::TREE;
        $this->enablePagination(false);
        $this->canSort(true);
        return $this;
    }

    public function asTreeMass($rootPid = 0, $keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository()->setKeyName($keyName);
        $this->repository()->setTreePidColumn($pidColumn);
        $this->repository()->setSortColumn($sortColumn);
        $this->repository()->setTreeTitleColumn($titleColumn);
        $this->engine = GridEngine::TREE_MASS;
        $this->enablePagination(false);
        $this->canSort(true);
        return $this;
    }

    
    public function canBatchDelete($value = null)
    {
        if (null === $value) {
            return $this->canBatchDelete;
        }
        $this->canBatchDelete = true;
        $this->canMultiSelectItem(true);
        return $this;
    }

    public function canBatchSelect($value = null)
    {
        if (null === $value) {
            return $this->canBatchSelect;
        }
        $this->canBatchSelect = true;
        $this->canMultiSelectItem(true);
        return $this;
    }

    public function disableCUD()
    {
        $this->canAdd(false)->canEdit(false)->canDelete(false);
        return $this;
    }

    public function dialogSizeSmall()
    {
        return $this
            ->addDialogSize(['600px', '90%'])
            ->editDialogSize(['600px', '90%'])
            ->showDialogSize(['600px', '90%']);
    }


    public function repository()
    {
        return $this->model->repository();
    }

    public function getRepositoryKeyName()
    {
        return $this->model->repository()->getKeyName();
    }

    
    public function gridFilter(Closure $callback)
    {
        call_user_func($callback, $this->gridFilter);
        return $this;
    }

    
    public function build()
    {
        if (!$this->isBuilt) {
            $this->runBuilder();
            $this->prepareItemOperateField();
            $this->isBuilt = true;
        }
    }

    public function request()
    {
        $addition = null;
        $this->build();
        $input = InputPackage::buildFromInput();
        $this->repository()->setArgument([
            'page' => $input->getPage(),
            'pageSize' => $input->getPageSize(),
            'order' => $input->getArray($this->model->getOrderName()),
            'orderDefault' => $this->defaultOrder,
        ]);
        $treeAncestors = [];
        if ($this->engine === GridEngine::TREE_MASS) {
            $pid = $input->get('_pid', $this->treeRootPid);
            $this->repository()->setArgument([
                'treeRootPid' => $this->treeRootPid,
                'treePid' => $pid,
            ]);
            if ($pid != $this->treeRootPid) {
                $treeAncestors = $this->repository()->getTreeAncestorItems();
            }
            $addition = view('modstart::core.grid.treeAncestor', [
                'treeAncestors' => $treeAncestors,
                'grid' => $this,
            ])->render();
        }
        $this->gridFilter->setSearch($input->getArray('search'));
        $items = $this->gridFilter->execute();
        if ($this->engine == GridEngine::TREE) {
            $treeIdName = $this->repository()->getKeyName();
            $treePidName = $this->repository()->getTreePidColumn();
            $treeSortName = $this->repository()->getTreeSortColumn();
                        $items = TreeUtil::itemsMergeLevel($items, $treeIdName, $treePidName, $treeSortName);
        }
        $paginator = $this->model->paginator();
        if ($this->hookPrepareItems) {
            $items = call_user_func($this->hookPrepareItems, $this, $items);
        }
        $records = [];
        foreach ($items as $index => $item) {
                        
            $itemColumns = [];
            if ($item instanceof \Illuminate\Database\Eloquent\Model) {
                $itemColumns = array_keys($item->getAttributes());
            } else if ($item instanceof \stdClass) {
                $itemColumns = array_keys(get_object_vars($item));
            } else {
                BizException::throws('Grid item support Model|stdClass only');
            }
            $record = [];
            $record['_id'] = $item->{$this->repository()->getKeyName()};
            foreach ($this->listableFields() as $field) {
                
                if ($field->isLayoutField()) {
                    continue;
                }
                $value = null;
                if (in_array($field->column(), $itemColumns)
                    || ($item instanceof \Illuminate\Database\Eloquent\Model && method_exists($item, $field->column()))
                ) {
                    $value = $item->{$field->column()};
                    $field->item($item);
                    if ($field->hookValueUnserialize()) {
                        $value = call_user_func($field->hookValueUnserialize(), $value, $field);
                    }
                    $field->item($item);
                    $value = $field->unserializeValue($value, $field);
                    if ($field->hookFormatValue()) {
                        $value = call_user_func($field->hookFormatValue(), $value, $field);
                    }
                    $item->{$field->column()} = $value;
                } else {
                    $field->item($item);
                    if ($field->hookValueUnserialize()) {
                        $value = call_user_func($field->hookValueUnserialize(), $value, $field);
                    }
                    $value = $field->unserializeValue($value, $field);
                    if ($field->hookFormatValue()) {
                        $value = call_user_func($field->hookFormatValue(), $value, $field);
                    }
                }
                $field->setValue($value);
                                $field->item($item);
                                $record[$field->column()] = $field->renderView($field, $item, $index);
                if ($this->engine == GridEngine::TREE && $field->column() == $this->repository()->getTreeTitleColumn()) {
                    $treePrefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $item->_level - 1)
                        . '<a class="tree-arrow-icon ub-text-muted" href="javascript:;"><i class="icon iconfont icon-angle-right"></i></a> ';
                    $record[$field->column()] = $treePrefix . htmlspecialchars($record[$field->column()]);
                } else if ($this->engine == GridEngine::TREE_MASS && $field->column() == $this->repository()->getTreeTitleColumn()) {
                    if (count($treeAncestors) < $this->treeMaxLevel() - 1) {
                        $record[$field->column()] =
                            '<span class="tree-arrow-icon ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span>'
                            . '<a class="ub-text-primary" href="?_pid=' . $record['_id'] . '" title="' . L('Manage') . '"><i class="icon iconfont icon-sign"></i> ' . htmlspecialchars($record[$field->column()]) . '</a>';
                    } else {
                        $record[$field->column()] =
                            '<span class="tree-arrow-icon ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span>'
                            . htmlspecialchars($record[$field->column()]);
                    }
                }
            }
            $records[] = $record;
        }
        $head = [];
        foreach ($this->listableFields() as $field) {
            if ($field->isLayoutField()) {
                continue;
            }
            $record = [
                'field' => $field->column(),
                'title' => $field->label(),
                'sort' => $field->sortable(),
            ];
            if ($field->width()) {
                $record['width'] = $field->width();
            }
            $head[] = $record;
        }
        return Response::jsonSuccessData([
            'head' => $head,
            'page' => $paginator ? $paginator->currentPage() : 1,
            'pageSize' => $paginator ? $paginator->perPage() : count($records),
            'total' => $paginator ? $paginator->total() : count($records),
            'records' => $records,
            'addition' => $addition,
        ]);
    }

    public function render()
    {
        $this->build();
        $data = array_merge($this->fluentAttributeVariables(), [
            'id' => $this->id,
            'filters' => $this->gridFilter->filters(),
            'grid' => $this,
        ]);
        return view($this->view, $data)->render();
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'hookSaved':
            case 'hookDeleting':
            case 'hookChanged':
            case 'hookDeleted':
            case 'formClass':
            case 'gridFilter':
                return $this;
        }
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        return FieldManager::call($this, $method, $arguments);
    }
}
