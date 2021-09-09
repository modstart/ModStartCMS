<?php

namespace ModStart\Repository;

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
use ModStart\Core\Type\SortDirection;
use ModStart\Core\Util\TreeUtil;
use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Form\Type\FormEngine;

class EloquentRepository extends Repository
{
    
    protected $eloquentClass;

    
    protected $model;

    
    protected $queryBuilder;

    
    protected $relations = [];

    
    protected $collection;

    
    public function __construct($modelOrRelations = [])
    {
        $this->initModel($modelOrRelations);
    }

    
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


    
    public function getCreatedAtColumn()
    {
        return $this->eloquent()->getCreatedAtColumn();
    }

    
    public function getUpdatedAtColumn()
    {
        return $this->eloquent()->getUpdatedAtColumn();
    }

    
    public function getTableColumns()
    {
        return ['*'];
    }

    
    public function getFormColumns()
    {
        return ['*'];
    }

    
    public function getShowColumns()
    {
        return ['*'];
    }

    
    public function with($relations)
    {
        $this->relations = (array)$relations;
        return $this;
    }


    
    public function get(\ModStart\Grid\Model $model)
    {
        $this->setOrder($model);
        $this->setPaginate($model);
        $query = $this->newQuery();
        if ($this->relations) {
            $query->with($this->relations);
        }
        $treePid = $this->getArgument('treePid', null);
        if (null !== $treePid) {
            $query->where($this->getTreePidColumn(), $treePid);
        }
        $model->grid()->repositoryFilter()->executeQueries($query);
        $model->getQueries()->each(function ($value) use (&$query) {
            if ($value['method'] == 'paginate') {
                $value['arguments'][1] = $this->getTableColumns();
            } elseif ($value['method'] == 'get') {
                $value['arguments'] = [$this->getTableColumns()];
            }
            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ? $value['arguments'] : []);
        });
        return $query;
    }


    
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
            if (Str::contains($column, '.')) {
                exit('Under Dev');
                            } else {
                $model->addQuery('orderBy', [$column, $type]);
            }
        }
    }

    

    
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

    
    protected function resolvePaginateArguments(\ModStart\Grid\Model $model, $paginate)
    {
        
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

    
    public function show(Detail $detail)
    {
        $query = $this->newQuery();
        $detail->repositoryFilter()->executeQueries($query);
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        $this->model = $query
            ->with($this->getRelations())
            ->findOrFail($detail->itemId(), $this->getShowColumns());

        return $this->model;
    }

    private function getMaxValue($field)
    {
        $query = $this->newQuery();
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        return $query->with($this->getRelations())->max($field);
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
                    $model->setAttribute($this->getTreeSortColumn(), $this->getMaxValue($this->getTreeSortColumn()) + 1);
                }
                if ($form->canSort()) {
                    $sortColumn = $this->getSortColumn();
                    if (empty($model->getAttribute($sortColumn))) {
                        $model->setAttribute($sortColumn, $this->getMaxValue($sortColumn) + 1);
                    }
                }
                $result = $model->save();
                $this->updateRelation($form, $model, $relations, $relationKeyMap);
                $form->item($model);
                ResultException::throwsIfFail($form->hookCall($form->hookSaved()));
                ResultException::throwsIfFail($form->hookCall($form->hookChanged()));
            });
        } catch (\Exception $e) {
            if (Str::contains($e->getMessage(), 'Duplicate entry')) {
                BizException::throws(L('Records Duplicated'));
            } else {
                throw $e;
            }
        }
        return $model->getKey();
    }

    
    public function edit(Form $form)
    {
        
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
            if (Str::contains($e->getMessage(), 'Duplicate entry')) {
                BizException::throws(L('Records Duplicated'));
            } else {
                throw $e;
            }
        }
        return $result;
    }

    public function sortEdit(Form $form)
    {
        $direction = $this->getArgument('direction');
        $query = $this->newQuery();
        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }
        $this->model = $query
            ->with($this->getRelations())
            ->find($form->itemId(), $this->getFormColumns());
        $queryAll = $this->newQuery();
        if ($this->isSoftDeletes) {
            $queryAll->withTrashed();
        }
        
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


    
    public function delete(Form $form, Arrayable $originalData)
    {
        $models = $this->collection->keyBy($this->getKeyName());
        DB::transaction(function () use ($form, $models) {
            ResultException::throwsIfFail($form->hookCall($form->hookDeleting()));
            collect($form->itemId())->filter()->each(function ($id) use ($form, $models) {
                
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
                                        $model->forceDelete();
                    return;
                } elseif (!$this->isSoftDeletes) {
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

    public function getTreeItems()
    {
        $query = $this->newQuery();
        if ($this->relations) {
            $query->with($this->relations);
        }
        
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


    
    protected function newQuery()
    {
        if ($this->queryBuilder) {
            return clone $this->queryBuilder;
        }
        $builder = $this->eloquent()->newQuery();
        return $builder;
    }

    
    public function eloquent()
    {
        return $this->model ? $this->model : ($this->model = $this->createEloquent());
    }

    
    public function createEloquent(array $data = [])
    {
        $model = new $this->eloquentClass();
        if ($data) {
            $model->setRawAttributes($data);
        }
        return $model;
    }

    
    private function getRelations()
    {
        return $this->relations;
    }

    
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

                                        if (is_null($parent)) {
                        $parent = $relation->getRelated();
                    }

                    foreach ($prepared[$name] as $column => $value) {
                        $parent->setAttribute($column, $value);
                    }

                    $parent->save();

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
