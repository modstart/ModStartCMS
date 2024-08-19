<?php

namespace ModStart\Grid;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use ModStart\Grid\Filter\AbstractFilter;
use ModStart\Grid\Filter\Eq;
use ModStart\Grid\Filter\Field\AbstractFilterField;
use ModStart\Grid\Filter\Has;
use ModStart\Grid\Filter\Like;
use ModStart\Grid\Filter\Likes;
use ModStart\Grid\Filter\Range;
use ReflectionClass;

/**
 * Class Filter.
 *
 * @method Eq               eq($column, $label = '')
 * @method Like             like($column, $label = '')
 * @method Likes            likes($column, $label = '')
 * @method Range            range($column, $label = '')
 * @method Has              has($column, $label = '')
 */
class GridFilter
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var AbstractFilter[]
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $supports = [
        'eq',
        'like',
        'likes',
        'range',
        'has',
    ];

    /**
     * @var AbstractFilterField
     */
    private $field;

    /**
     * @var array
     */
    private $search;

    /**
     * 动态范围条件
     * @var Collection
     */
    private $scopes;

    /**
     * Create a new filter instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model = null)
    {
        $this->model = $model;
        $this->fixedScopes = collect();
        $this->scopes = collect();
    }

    /**
     * @param $search
     * @return $this
     */
    public function setSearch(array $search)
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @return GridFilterScope
     */
    public function scope()
    {
        $scope = new GridFilterScope($this);
        $this->scopes->push($scope);
        return $scope;
    }

    /**
     * @param AbstractFilter $filter
     * @return AbstractFilter
     */
    public function addFilter(AbstractFilter $filter)
    {
        $filter->setTableFilter($this);
        return $this->filters[] = $filter;
    }

    /**
     * 清空Filter
     */
    public function clearFilter()
    {
        $this->filters = [];
    }

    /**
     * 根据字段删除指定的Filter
     * @param $column
     */
    public function deleteFilterByColumn($column)
    {
        $this->filters = array_filter($this->filters, function ($filter) use ($column) {
            return $filter->column() != $column;
        });
    }

    /**
     * Get all queries.
     *
     * @return AbstractFilter[]
     */
    public function filters()
    {
        return $this->filters;
    }

    public function hasVisibleFilters()
    {
        foreach ($this->filters as $filter) {
            if (!$filter->hidden()) {
                return true;
            }
        }
        return false;
    }

    public function hasAutoHideFilters()
    {
        foreach ($this->filters as $filter) {
            if ($filter->autoHide()) {
                return true;
            }
        }
        return false;
    }


    public function getConditions()
    {
        $conditions = [];
        $search = $this->search;
        if (!empty($search)) {
            foreach ($search as $searchGroup) {
                if (!is_array($searchGroup)) {
                    continue;
                }
                foreach ($searchGroup as $columnName => $queryInfo) {
                    foreach ($this->filters() as $filter) {
                        if ($columnName === $filter->column() && isset($queryInfo[$filter->name()])) {
                            $condition = $filter->condition($queryInfo);
                            if (!empty($condition)) {
                                $keys = array_keys($condition);
                                if (count($keys) > 1 || (count($keys) == 1 && $keys[0] === 0)) {
                                    $conditions = array_merge($conditions, $condition);
                                } else {
                                    $conditions[] = $condition;
                                }
                            }
                        }
                    }
                }
            }
        }
        // var_dump($search);exit();
        return array_filter($conditions);
    }

    /**
     * 对一个Query执行所有的条件
     * @param $query Builder
     * @return Builder
     */
    public function executeQueryConditions($query)
    {
        $conditions = array_merge(
            $this->getScopeConditions(),
            $this->getConditions()
        );
        foreach ($conditions as $condition) {
            $query = call_user_func_array([$query, key($condition)], current($condition));
        }
        return $query;
    }

    public function executeQuery()
    {
        $conditions = array_merge(
            $this->getScopeConditions(),
            $this->getConditions()
        );
        // print_r($conditions);exit();
        $this->model->clearQuery();
        return $this->model->addConditions($conditions)->getConditionQuery();
    }

    public function execute()
    {
        $conditions = array_merge(
            $this->getScopeConditions(),
            $this->getConditions()
        );
        $this->model->clearQuery();
        $grid = $this->model->grid();
        $preQuery = $grid->hookPreQuery();
        if ($preQuery) {
            call_user_func($preQuery, $grid);
        }
        // print_r($conditions);exit();
        // print_r($this->model->getQueries()->toArray());exit();
        return $this->model->addConditions($conditions)->buildData();
    }

    private function getScopeConditions()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $method
     * @param $arguments
     * @return AbstractFilter
     * @throws \ReflectionException
     */
    public function __call($method, $arguments)
    {
        if (in_array($method, $this->supports)) {
            $className = '\\ModStart\\Grid\\Filter\\' . ucfirst($method);
            $reflection = new ReflectionClass($className);
            /** @var AbstractFilter $filter */
            $filter = $reflection->newInstanceArgs($arguments);
            if ($this->model) {
                $filter->grid($this->model->grid());
            }
            return $this->addFilter($filter);
        }
    }
}
