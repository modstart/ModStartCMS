<?php

namespace ModStart\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Exception\ResultException;
use ModStart\Core\Type\SortAddPosition;
use ModStart\Core\Type\SortDirection;
use ModStart\Core\Util\ExceptionUtil;
use ModStart\Core\Util\ReUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Form\Type\FormEngine;

class EloquentRepository extends Repository
{
    /**
     * @var string
     */
    protected $eloquentClass;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $collection;

    /**
     * EloquentRepository constructor.
     *
     * @param Model|array|string $modelOrRelations $modelOrRelations
     */
    public function __construct($modelOrRelations = [])
    {
        $this->initModel($modelOrRelations);
    }

    /**
     * 初始化模型.
     *
     * @param Model|Builder|array|string $modelOrRelations
     */
    protected function initModel($modelOrRelations)
    {
        if (is_string($modelOrRelations) && class_exists($modelOrRelations)) {
            $this->eloquentClass = $modelOrRelations;
        } elseif ($modelOrRelations instanceof Model) {
            $this->eloquentClass = get_class($modelOrRelations);
            $this->model = $modelOrRelations;
        } elseif ($modelOrRelations instanceof Builder) {
            $this->model = $modelOrRelations->getModel();
            $this->eloquentClass = get_class($this->model);
            $this->queryBuilder = $modelOrRelations;
        } else {
            $this->with($modelOrRelations);
        }

        $this->setKeyName($this->eloquent()->getKeyName());

        $this->setIsSoftDeletes(
            in_array(SoftDeletes::class, class_uses($this->eloquent()))
        );
    }

    public function setKeyName($keyName)
    {
        parent::setKeyName($keyName);
        if ($this->model && $this->model instanceof DynamicModel) {
            $this->model->setKeyName($keyName);
        }
    }


    /**
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return $this->eloquent()->getCreatedAtColumn();
    }

    /**
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return $this->eloquent()->getUpdatedAtColumn();
    }

    /**
     * 获取列表页面查询的字段.
     *
     * @return array
     */
    public function getTableColumns()
    {
        return ['*'];
    }

    /**
     * 获取表单页面查询的字段.
     *
     * @return array
     */
    public function getFormColumns()
    {
        return ['*'];
    }

    /**
     * 获取详情页面查询的字段.
     *
     * @return array
     */
    public function getShowColumns()
    {
        return ['*'];
    }

    /**
     * 设置关联关系.
     *
     * @param mixed $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->relations = (array)$relations;
        return $this;
    }

    public function getQuery(\ModStart\Grid\Model $model)
    {
        $this->setOrder($model);
        // $this->setPaginate($model);
        $query = $this->newQuery();
        if ($this->relations) {
            $query->with($this->relations);
        }
        $treePid = $this->getArgument('treePid', null);
        if (null !== $treePid) {
            $query->where($this->getTreePidColumn(), $treePid);
        }
        $model->grid()->repositoryFilter()->executeQueries($query);
        $model->grid()->scopeExecuteQueries($query);
        $tableColumns = $this->getTableColumns();
        if ($model->grid()->isDynamicModel()) {
            foreach ($tableColumns as $k => $v) {
                if ($v == '*') {
                    $tableColumns[$k] = $model->grid()->getDynamicModelTableName() . '.*';
                }
            }
        }
        // var_dump($model->getQueries());exit();
        // var_dump($model->grid()->isDynamicModel());exit();
        $joins = $model->grid()->gridFilterJoins();
        // var_dump($joins);
        if (!empty($joins)) {
            $methodMap = [
                'left' => 'leftJoin',
                'right' => 'rightJoin',
                'inner' => 'innerJoin',
            ];
            foreach ($joins as $join) {
                $mode = $join[0];
                array_shift($join);
                call_user_func_array([$query, $methodMap[$mode]], $join);
            }
        }
        // var_dump($model->getQueries());exit();
        $model->getQueries()->each(function ($value) use (&$query, $tableColumns) {
            if ($value['method'] == 'paginate') {
                $value['arguments'][1] = $tableColumns;
            } elseif ($value['method'] == 'get') {
                $value['arguments'] = [$tableColumns];
            }
            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ? $value['arguments'] : []);
        });
        return $query;
    }


    /**
     * execute paginate or get all records.
     *
     * @param \ModStart\Grid\Model $model
     *
     * @return LengthAwarePaginator|Collection|array
     */
    public function get(\ModStart\Grid\Model $model)
    {
        // print_r($model->getQueries()->toArray());exit();
        $this->setOrder($model);
        $this->setPaginate($model);
        $query = $this->newQuery();
        // var_dump($this->relations);exit();
        // $this->relations = ['doc','memberUser'];
        // var_dump($this->relations);exit();
        if ($this->relations) {
            $query->with($this->relations);
        }
        // print_r($model->getQueries()->toArray());exit();
        $treePid = $this->getArgument('treePid', null);
        if (null !== $treePid) {
            $query->where($this->getTreePidColumn(), $treePid);
        }
        $model->grid()->repositoryFilter()->executeQueries($query);
        $model->grid()->scopeExecuteQueries($query);
        $tableColumns = $this->getTableColumns();
        // var_dump($model->grid()->isDynamicModel());exit();
        if ($model->grid()->isDynamicModel()) {
            foreach ($tableColumns as $k => $v) {
                if ($v == '*') {
                    $tableColumns[$k] = $model->grid()->getDynamicModelTableName() . '.*';
                }
            }
        }
        // print_r($model->getQueries()->toArray());exit();
        // var_dump($model->grid()->isDynamicModel());exit();
        $joins = $model->grid()->gridFilterJoins();
        // var_dump($joins);exit();
        if (!empty($joins)) {
            $methodMap = [
                'left' => 'leftJoin',
                'right' => 'rightJoin',
                'inner' => 'innerJoin',
            ];
            foreach ($joins as $join) {
                $mode = $join[0];
                array_shift($join);
                call_user_func_array([$query, $methodMap[$mode]], $join);
            }
        }
        // print_r($model->getQueries()->toArray());exit();
        $model->getQueries()->each(function ($value) use (&$query, $tableColumns) {
            // print_r($value);
            if ($value['method'] == 'paginate') {
                $value['arguments'][1] = $tableColumns;
            } elseif ($value['method'] == 'get') {
                $value['arguments'] = [$tableColumns];
            }
            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ? $value['arguments'] : []);
        });
        // var_dump($query);
        // var_dump($this->relations);exit();
        return $query;
    }


    /**
     * 设置表格数据排序.
     *
     * @param \ModStart\Grid\Model $model
     *
     * @return void
     */
    protected function setOrder(\ModStart\Grid\Model $model)
    {
        $order = $model->getOrder();
        if (empty($order)) {
            return;
        }
        $model->resetOrderBy();
        foreach ($order as $orderItem) {
            list($column, $type) = $orderItem;
            if (empty($column) || empty($type)) {
                continue;
            }
            $model->addQuery('orderBy', [$column, $type]);
        }
    }

    /**
     * 设置分页参数.
     *
     * @param \ModStart\Grid\Model $model
     *
     * @return void
     */
    protected function setPaginate(\ModStart\Grid\Model $model)
    {
        $paginate = $model->findQueryByMethod('paginate');
        $model->rejectQuery(['paginate']);
        if (!$model->allowPagination()) {
            $model->addQuery('get', [$this->getTableColumns()]);
        } else {
            $model->addQuery('paginate', $this->resolvePaginateArguments($model, $paginate));
        }
    }

    /**
     * 获取分页参数.
     *
     * @param \ModStart\Grid\Model $model
     * @param array|Model|null $paginate
     *
     * @return array
     */
    protected function resolvePaginateArguments(\ModStart\Grid\Model $model, $paginate)
    {
        /**
         * 返回分页函数调用参数
         * model->paginate($pageSize, ['*'], 'page', $page)
         */
        if ($paginate && is_array($paginate)) {
            if ($pageSize = intval($this->getArgument('pageSize'))) {
                $paginate['arguments'][0] = $pageSize;
            }
            return $paginate['arguments'];
        }
        return [
            $model->getPageSize(),
            $this->getTableColumns(),
            $model->getPageName(),
            $model->getPage(),
        ];
    }

    /**
     * 查询编辑页面数据.
     *
     * @param Form $form
     *
     * @return Model
     */
    public function editing(Form $form)
    {
        $query = $this->newQuery();
        $form->repositoryFilter()->executeQueries($query);
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        $this->model = $query
            ->with($this->getRelations())
            ->find($form->itemId(), $this->getFormColumns());
        return $this->model;
    }

    /**
     * 查询详情页面数据.
     *
     * @param Detail $show
     *
     * @return Model
     */
    public function show(Detail $detail)
    {
        $query = $this->newQuery();
        $detail->repositoryFilter()->executeQueries($query);
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        $this->model = $query
            ->with($this->getRelations())
            ->find($detail->itemId(), $this->getShowColumns());

        return $this->model;
    }

    private function prepareExistsSort($field, Form $form)
    {
        $query = $this->newQuery();
        $form->repositoryFilter()->executeQueries($query);
        $form->scopeExecuteQueries($query);
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        $query->update([$field => DB::raw("`$field` + 1")]);
    }

    private function getNextSortValue($field, Form $form)
    {
        switch ($form->sortAddPosition()) {
            case SortAddPosition::HEAD:
                $value = 1;
                break;
            case SortAddPosition::TAIL:
            default:
                $query = $this->newQuery();
                $form->repositoryFilter()->executeQueries($query);
                $form->scopeExecuteQueries($query);
                if ($this->isSoftDeletes) {
                    $query->withTrashed();
                }
                $value = intval($query->with($this->getRelations())->max($field)) + 1;
                break;
        }
        return $value;
    }

    public function add(Form $form)
    {
        $result = null;
        $model = $this->eloquent();
        try {
            DB::transaction(function () use ($form, &$result, $model) {
                ResultException::throwsIfFail($form->hookCall($form->hookSaving()));
                list($relations, $relationKeyMap) = $this->getRelationInputs($model, $form->dataAdding());
                if ($relations) {
                    $form->dataAdding(array_except($form->dataAdding(), array_keys($relations)));
                }
                foreach ($form->dataAdding() as $column => $value) {
                    $model->setAttribute($column, $value);
                }
                if ($form->engine() == FormEngine::TREE || $form->engine() == FormEngine::TREE_MASS) {
                    if ($model->getAttribute($this->getTreePidColumn())) {
                        BizException::throwsIf(L('Parent Item Not Exists'), !TreeUtil::modelItemAddAble($model, $model->getAttribute($this->getTreePidColumn()), $this->getKeyName()));
                    }
                    $model->setAttribute($this->getTreeSortColumn(), $this->getNextSortValue($this->getTreeSortColumn(), $form));
                }
                if ($form->canSort()) {
                    $sortColumn = $this->getSortColumn();
                    if (empty($model->getAttribute($sortColumn))) {
                        $model->setAttribute($sortColumn, $this->getNextSortValue($sortColumn, $form));
                    }
                }
                foreach ($form->scopeAddedParam() as $k => $v) {
                    $model->setAttribute($k, $v);
                }
                switch ($form->sortAddPosition()) {
                    case SortAddPosition::HEAD:
                        $this->prepareExistsSort($this->getSortColumn(), $form);
                        break;
                }
                $result = $model->save();
                $this->updateRelation($form, $model, $relations, $relationKeyMap);
                $form->item($model);
                ResultException::throwsIfFail($form->hookCall($form->hookSaved()));
                ResultException::throwsIfFail($form->hookCall($form->hookChanged()));
            });
        } catch (\Exception $e) {
            ExceptionUtil::throwExcpectException($e);
        }
        return $model->getKey();
    }

    /**
     * 更新数据
     *
     * @param Form $form
     * @return array|Arrayable|void|null
     * @throws BizException
     */
    public function edit(Form $form)
    {
        /* @var Model $builder */
        $model = $this->eloquent();
        if (!$model->getKey()) {
            $model->exists = true;
            $model->setAttribute($model->getKeyName(), $form->itemId());
        }
        $result = null;
        try {
            DB::transaction(function () use ($form, $model, &$result) {
                ResultException::throwsIfFail($form->hookCall($form->hookSaving()));
                list($relations, $relationKeyMap) = $this->getRelationInputs($model, $form->dataEditing());
                if ($relations) {
                    $form->dataEditing(array_except($form->dataEditing(), array_keys($relationKeyMap)));
                }
                foreach ($form->dataEditing() as $column => $value) {
                    $model->setAttribute($column, $value);
                }
                if ($form->engine() == FormEngine::TREE || $form->engine() == FormEngine::TREE_MASS) {
                    $exists = $model->newQuery()->where([$this->getKeyName() => $form->itemId()])->first($this->getFormColumns());
                    BizException::throwsIf(L('Parent Error'), !TreeUtil::modelItemChangeAble(
                        $model,
                        $form->itemId(),
                        $exists->getAttribute($this->getTreePidColumn()),
                        $model->getAttribute($this->getTreePidColumn()),
                        $this->getKeyName(),
                        $this->getTreePidColumn()
                    ));
                }
                $result = $model->update();
                $this->updateRelation($form, $model, $relations, $relationKeyMap);
                ResultException::throwsIfFail($form->hookCall($form->hookSaved()));
                ResultException::throwsIfFail($form->hookCall($form->hookChanged()));
            });
        } catch (\Exception $e) {
            ExceptionUtil::throwExcpectException($e);
        }
        return $result;
    }

    public function sortEdit(Form $form)
    {
        $direction = $this->getArgument('direction');
        $query = $this->newQuery();
        $form->repositoryFilter()->executeQueries($query);
        $form->scopeExecuteQueries($query);
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        $this->model = $query
            ->with($this->getRelations())
            ->find($form->itemId(), $this->getFormColumns());
        $queryAll = $this->newQuery();
        $form->repositoryFilter()->executeQueries($queryAll);
        $form->scopeExecuteQueries($queryAll);
        if ($this->isSoftDeletes) {
            $queryAll->withTrashed();
        }
        /** @var Collection $allModels */
        if ($form->engine() === FormEngine::TREE || $form->engine() === FormEngine::TREE_MASS) {
            $sortColumn = $this->getTreeSortColumn();
            $allModels = $queryAll
                ->with($this->getRelations())
                ->where([$this->getTreePidColumn() => $this->model->getAttribute($this->getTreePidColumn())])
                ->orderBy($sortColumn, 'asc')
                ->get([$this->getKeyName(), $sortColumn]);
        } else {
            $sortColumn = $this->getSortColumn();
            $allModels = $queryAll
                ->with($this->getRelations())
                ->orderBy($sortColumn, 'asc')
                ->get([$this->getKeyName(), $sortColumn]);
        }
        $sort = 1;
        $allModels->each(function ($item) use ($sortColumn, &$sort) {
            $currentSort = $sort++;
            if ($currentSort !== $item->{$sortColumn}) {
                $item->{$sortColumn} = $currentSort;
                $item->save();
            }
        });
        $existsIndex = -1;
        foreach ($allModels as $index => $item) {
            if ($item->{$this->getKeyName()} == $form->itemId()) {
                $existsIndex = $index;
                break;
            }
        }
        // var_dump($existsIndex);print_r($allModels);exit();
        BizException::throwsIf('Sort id not found', $existsIndex < 0);
        $form->item($allModels);
        switch ($direction) {
            case SortDirection::UP:
                if ($existsIndex > 0) {
                    $oldSort = $allModels->get($existsIndex)->{$sortColumn};
                    $allModels->get($existsIndex)->{$sortColumn} = $allModels->get($existsIndex - 1)->{$sortColumn};
                    $allModels->get($existsIndex)->save();
                    $allModels->get($existsIndex - 1)->{$sortColumn} = $oldSort;
                    $allModels->get($existsIndex - 1)->save();
                }
                break;
            case SortDirection::DOWN:
                if ($existsIndex < $allModels->count() - 1) {
                    $oldSort = $allModels->get($existsIndex)->{$sortColumn};
                    $allModels->get($existsIndex)->{$sortColumn} = $allModels->get($existsIndex + 1)->{$sortColumn};
                    $allModels->get($existsIndex)->save();
                    $allModels->get($existsIndex + 1)->{$sortColumn} = $oldSort;
                    $allModels->get($existsIndex + 1)->save();
                }
                break;
            case SortDirection::TOP:
                if ($existsIndex > 0) {
                    $sort = 2;
                    $allModels->each(function ($item, $i) use ($existsIndex, $sortColumn, &$sort) {
                        $old = $item->{$sortColumn};
                        if ($i == $existsIndex) {
                            $item->{$sortColumn} = 1;
                        } else {
                            $item->{$sortColumn} = $sort++;
                        }
                        if ($old != $item->{$sortColumn}) {
                            $item->save();
                        }
                    });
                }
                break;
            case SortDirection::BOTTOM:
                if ($existsIndex < $allModels->count() - 1) {
                    $sort = 1;
                    $allModels->each(function ($item, $i) use ($existsIndex, $sortColumn, &$sort) {
                        $old = $item->{$sortColumn};
                        if ($i == $existsIndex) {
                            return;
                        } else {
                            $item->{$sortColumn} = $sort++;
                        }
                        if ($old != $item->{$sortColumn}) {
                            $item->save();
                        }
                    });
                    $allModels->get($existsIndex)->{$this->getSortColumn()} = $sort;
                    $allModels->get($existsIndex)->save();
                }
                break;
        }
    }


    /**
     * 删除数据.
     *
     * @param Form $form
     * @param array $originalData
     *
     * @return bool
     */
    public function delete(Form $form, Arrayable $originalData)
    {
        $models = $this->collection->keyBy($this->getKeyName());
        DB::transaction(function () use ($form, $models) {
            ResultException::throwsIfFail($form->hookCall($form->hookDeleting()));
            collect($form->itemId())->filter()->each(function ($id) use ($form, $models) {
                /** @var Model $model */
                $model = $models->get($id);
                if (!$model) {
                    return;
                }
                $data = $model->toArray();
                if ($form->engine() == FormEngine::TREE || $form->engine() == FormEngine::TREE_MASS) {
                    BizException::throwsIf(L('Delete Error (has children node)'),
                        !TreeUtil::modelItemDeleteAble($model, $form->itemId(), $this->getTreePidColumn())
                    );
                }
                if ($this->isSoftDeletes && $model->trashed()) {
                    // $form->deleteFiles($data, true);
                    $model->forceDelete();
                    return;
                } elseif (!$this->isSoftDeletes) {
                    // $form->deleteFiles($data);
                }
                $model->delete();
            });
            ResultException::throwsIfFail($form->hookCall($form->hookDeleted()));
            ResultException::throwsIfFail($form->hookCall($form->hookChanged()));
        });
        return true;
    }

    public function deleting(Form $form)
    {
        $query = $this->newQuery();
        $form->repositoryFilter()->executeQueries($query);
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        $id = $form->itemId();
        $this->collection = $query
            ->with($this->getRelations())
            ->find($id, $this->getFormColumns());
        return $this->collection;
    }

    public function getTreePidColumn()
    {
        $model = $this->eloquent();
        if (method_exists($model, 'getTreePidColumn')) {
            return $model->getTreePidColumn();
        }
        return parent::getTreePidColumn();
    }

    public function getTreeTitleColumn()
    {
        $model = $this->eloquent();
        if (method_exists($model, 'getTreeTitleColumn')) {
            return $model->getTreeTitleColumn();
        }
        return parent::getTreeTitleColumn();
    }

    public function getTreeSortColumn()
    {
        $model = $this->eloquent();
        if (method_exists($model, 'getTreeSortColumn')) {
            return $model->getTreeSortColumn();
        }
        return parent::getTreeSortColumn();
    }

    public function getTreeItems($context)
    {
        $query = $this->newQuery();
        if ($this->relations) {
            $query->with($this->relations);
        }
        $context->scopeExecuteQueries($query);
        /** @var Collection $collection */
        $collection = $query
            ->orderBy($this->getTreeSortColumn(), 'ASC')
            ->get($this->getTableColumns());
        $treeIdName = $this->getKeyName();
        $treePidName = $this->getTreePidColumn();
        $treeSortName = $this->getTreeSortColumn();
        $items = TreeUtil::itemsMergeLevel($collection, $treeIdName, $treePidName, $treeSortName);
        return $items;
    }

    public function getTreeAncestorItems()
    {
        $treePid = $this->getArgument('treePid', null);
        $ancestors = [];
        for ($i = 0, $pid = $treePid; $i < 99; $i++) {
            if (!$pid) {
                break;
            }
            $query = $this->newQuery();
            if ($this->relations) {
                $query->with($this->relations);
            }
            $query->where([$this->getKeyName() => $pid]);
            $item = $query->first($this->getTableColumns());
            if (!$item) {
                break;
            }
            $ancestors[] = $item;
            $pid = $item->{$this->getTreePidColumn()};
        }
        return array_reverse($ancestors);
    }


    /**
     * @return Builder
     */
    protected function newQuery()
    {
        if ($this->queryBuilder) {
            return clone $this->queryBuilder;
        }
        $builder = $this->eloquent()->newQuery();
        return $builder;
    }

    /**
     * 获取model对象
     *
     * @return Model
     */
    public function eloquent()
    {
        return $this->model ? $this->model : ($this->model = $this->createEloquent());
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function createEloquent(array $data = [])
    {
        $model = new $this->eloquentClass();
        if ($data) {
            $model->setRawAttributes($data);
        }
        return $model;
    }

    /**
     * 获取模型的所有关联关系.
     *
     * @return array
     */
    private function getRelations()
    {
        return $this->relations;
    }

    /**
     * 获取模型关联关系的表单数据.
     *
     * @param Model $model
     * @param array $inputs
     *
     * @return array
     */
    private function getRelationInputs($model, $inputs = [])
    {
        $map = [];
        $relations = [];
        foreach ($inputs as $column => $value) {
            $relationColumn = null;
            if (method_exists($model, $column)) {
                $relationColumn = $column;
            } elseif (method_exists($model, $camelColumn = Str::camel($column))) {
                $relationColumn = $camelColumn;
            }
            if (!$relationColumn) {
                continue;
            }
            $relation = call_user_func([$model, $relationColumn]);
            if ($relation instanceof Relations\Relation) {
                $relations[$column] = $value;
                $map[$column] = $relationColumn;
            }
        }
        return [&$relations, $map];
    }

    /**
     * 更新关联关系数据.
     *
     * @param Form $form
     * @param Model $model
     * @param array $relationsData
     * @param array $relationKeyMap
     *
     * @throws \Exception
     */
    private function updateRelation(Form $form, Model $model, array $relationsData, array $relationKeyMap)
    {

        foreach ($relationsData as $name => $values) {
            $relationName = $relationKeyMap[$name] ? $relationKeyMap[$name] : $name;

            if (!method_exists($model, $relationName)) {
                continue;
            }

            $relation = $model->$relationName();

            $oneToOneRelation = $relation instanceof Relations\HasOne
                || $relation instanceof Relations\MorphOne
                || $relation instanceof Relations\BelongsTo;

            $prepared = $oneToOneRelation ? $form->prepareUpdate([$name => $values]) : [$name => $values];

            if (empty($prepared)) {
                continue;
            }

            switch (true) {
                case $relation instanceof Relations\BelongsToMany:
                case $relation instanceof Relations\MorphToMany:
                    if (isset($prepared[$name])) {
                        $relation->sync($prepared[$name]);
                    }
                    break;
                case $relation instanceof Relations\HasOne:

                    $related = $model->$relationName;

                    // if related is empty
                    if (is_null($related)) {
                        $related = $relation->getRelated();
                        $qualifiedParentKeyName = $relation->getQualifiedParentKeyName();
                        $localKey = Arr::last(explode('.', $qualifiedParentKeyName));
                        $related->{$relation->getForeignKeyName()} = $model->{$localKey};
                    }

                    foreach ($prepared[$name] as $column => $value) {
                        $related->setAttribute($column, $value);
                    }

                    $related->save();
                    break;
                case $relation instanceof Relations\BelongsTo:
                case $relation instanceof Relations\MorphTo:

                    $parent = $model->$relationName;

                    // if related is empty
                    if (is_null($parent)) {
                        $parent = $relation->getRelated();
                    }

                    foreach ($prepared[$name] as $column => $value) {
                        $parent->setAttribute($column, $value);
                    }

                    $parent->save();

                    // When in creating, associate two models
                    $foreignKeyMethod = version_compare(app()->version(), '5.8.0', '<') ? 'getForeignKey' : 'getForeignKeyName';
                    if (!$model->{$relation->{$foreignKeyMethod}()}) {
                        $model->{$relation->{$foreignKeyMethod}()} = $parent->getKey();

                        $model->save();
                    }

                    break;
                case $relation instanceof Relations\MorphOne:
                    $related = $model->$relationName;
                    if (is_null($related)) {
                        $related = $relation->make();
                    }
                    foreach ($prepared[$name] as $column => $value) {
                        $related->setAttribute($column, $value);
                    }
                    $related->save();
                    break;
                case $relation instanceof Relations\HasMany:
                case $relation instanceof Relations\MorphMany:

                    if (!is_array($prepared[$name])) {
                        $v = @json_decode($prepared[$name], true);
                        if (is_array($v)) {
                            $prepared[$name] = $v;
                        }
                    }
                    BizException::throwsIf("Field $name ( HasMany|MorphMany ) is not array", !is_array($prepared[$name]));

                    foreach ($prepared[$name] as $related) {
                        /** @var Relations\Relation $relation */
                        $relation = $model->$relationName();

                        $keyName = $relation->getRelated()->getKeyName();

                        $instance = $relation->findOrNew(Arr::get($related, $keyName));

                        if (Arr::get($related, '_remove_') == 1) {
                            $instance->delete();

                            continue;
                        }

                        Arr::forget($related, '_remove_');

                        $key = Arr::get($related, $relation->getModel()->getKeyName());
                        if ($key === null || $key === '') {
                            Arr::forget($related, $relation->getModel()->getKeyName());
                        }

                        $instance->fill($related);

                        $instance->save();
                    }

                    break;
            }
        }
    }
}
