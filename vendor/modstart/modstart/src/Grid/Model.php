<?php

namespace ModStart\Grid;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use ModStart\Field\AbstractField;
use ModStart\Repository\Repository;

/**
 * @mixin Builder
 */
class Model
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var AbstractPaginator
     */
    private $paginator;

    /**
     * Array of queries of the model.
     *
     * @var \Illuminate\Support\Collection
     */
    private $queries;

    /**
     * Sort parameters of the model.
     *
     * @var array
     */
    private $order;

    /**
     * @var Collection
     */
    private $data;

    /**
     * @var callable
     */
    private $builder;

    /**
     * 每页显示数量
     *
     * @var int
     */
    private $pageSize = 10;

    /**
     * @var string
     */
    private $pageName = 'page';

    /**
     * @var int
     */
    private $page;

    /**
     * If the model use pagination.
     *
     * @var bool
     */
    private $usePaginate = true;

    /**
     * The query string variable used to store the per-page.
     *
     * @var string
     */
    private $pageSizeName = 'pageSize';

    /**
     * The query string variable used to store the order.
     *
     * @var string
     */
    private $orderName = 'order';

    /**
     * Model constructor.
     * @param $grid
     * @param null $repository
     */
    public function __construct($grid, $repository = null)
    {
        $this->grid = $grid;
        if ($repository) {
            $this->repository = Repository::instance($repository);
        }
        $this->queries = new Collection();
    }

    /**
     * @return Repository|null
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * @return $this
     */
    public function clearQuery()
    {
        if (!$this->queries->isEmpty()) {
            $this->queries = new Collection();
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getQueries()
    {
        return $this->queries = $this->queries->unique();
    }

    /**
     * @return AbstractPaginator
     * @throws \Exception
     */
    public function paginator()
    {
        $this->buildData();
        return $this->paginator;
    }

    /**
     * Enable or disable pagination.
     *
     * @param bool $use
     */
    public function usePaginate($use = true)
    {
        $this->usePaginate = $use;
    }

    /**
     * @return bool
     */
    public function allowPagination()
    {
        return $this->usePaginate;
    }

    /**
     * Get the query string variable used to store the per-page.
     *
     * @return string
     */
    public function getPageSizeName()
    {
        return $this->pageSizeName;
    }

    /**
     * Set the query string variable used to store the per-page.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setPageSizeName($name)
    {
        $this->pageSizeName = $name;
        return $this;
    }

    /**
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @param string $pageName
     * @return $this
     */
    public function setPageName(string $pageName)
    {
        $this->pageName = $pageName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * Get the query string variable used to store the order.
     *
     * @return string
     */
    public function getOrderName()
    {
        return $this->orderName;
    }

    /**
     * Get parent gird instance.
     *
     * @return Grid
     */
    public function grid()
    {
        return $this->grid;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function buildData()
    {
        if (is_null($this->data)) {
            $this->setData($this->fetch());
        }
        return $this->data;
    }

    /**
     * @param Collection|callable|array|AbstractPaginator $data
     *
     * @return $this
     */
    public function setData($data)
    {
        if (is_callable($data)) {
            $this->builder = $data;
            return $this;
        }
        if ($data instanceof AbstractPaginator) {
            $this->setPaginator($data);
            $data = $data->items();
        } elseif (is_array($data)) {
            $data = collect($data);
        } elseif ($data instanceof Collection) {
        } elseif ($data instanceof Arrayable) {
            $data = collect($data->toArray());
        }
        if ($data instanceof Collection) {
            $this->data = $data;
        } else {
            $this->data = collect();
        }
        //$this->stdObjToArray($this->data);
        return $this;
    }

    /**
     * Add conditions to grid model.
     *
     * @param array $conditions
     *
     * @return $this
     */
    public function addConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            call_user_func_array([$this, key($condition)], current($condition));
        }

        return $this;
    }

    public function getConditionQuery()
    {
        return $this->repository->getQuery($this);
    }

    /**
     * @return Collection|array
     * @throws \Exception
     *
     */
    private function fetch()
    {
        // print_r($this->getQueries()->toArray());exit();
        if ($this->paginator) {
            return $this->paginator->getCollection();
        }
        if ($this->builder && is_callable($this->builder)) {
            $results = call_user_func($this->builder, $this);
        } else {
            $results = $this->repository->get($this);
        }
        if (is_array($results) || $results instanceof Collection) {
            return $results;
        }
        if ($results instanceof AbstractPaginator) {
            $this->setPaginator($results);
            return $results->items();
        }
        throw new \Exception('Grid fetch error');
    }

    /**
     * @param AbstractPaginator $paginator
     *
     * @return void
     */
    private function setPaginator(AbstractPaginator $paginator)
    {
        $this->paginator = $paginator;
        $paginator->setPageName($this->pageName);
    }

    /**
     * Get current page.
     *
     * @return int|null
     */
    public function getPage()
    {
        if (!$this->usePaginate) {
            return;
        }
        return $this->page = $this->repository->getArgument($this->pageName, 1);
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get items number of per page.
     *
     * @return int|null
     */
    public function getPageSize()
    {
        if (!$this->usePaginate) {
            return;
        }
        return $this->repository->getArgument('pageSize', $this->pageSize);
    }

    /**
     * Find query by method name.
     *
     * @param $method
     *
     * @return static
     */
    public function findQueryByMethod($method)
    {
        return $this->queries->first(function ($query) use ($method) {
            return $query['method'] == $method;
        });
    }

    /**
     * 设定排序信息
     *
     * @param array $order
     * 单列 [ name',desc' ]
     * 多列 [ [name1,asc], [name2,desc] ]
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * 获取排序信息
     *
     * @return array
     * 单列 [ name',desc' ]
     * 多列 [ [name1,asc], [name2,desc] ]
     */
    public function getOrder()
    {
        $order = $this->repository->getArgument('order', []);
        $orderDefault = $this->repository->getArgument('orderDefault', []);
        // print_r($order);print_r($orderDefault);print_r($this->order);exit();
        if (empty($order)) {
            if (!empty($orderDefault)) {
                if (is_array($orderDefault[0])) {
                    $this->order = $orderDefault;
                } else {
                    $this->order = [$orderDefault];
                }
            } else {
                $this->order = [[$this->repository->getKeyName(), 'desc']];
            }
        } else {
            /** @var Collection $sortableColumns */
            $sortableColumns = $this->grid->sortableFields()->map(function (AbstractField $field) {
                return $field->column();
            });
            if (!is_array($order[0])) {
                $order = [$order];
            }
            $order = collect($order)->filter(function ($item) use ($sortableColumns) {
                return is_array($item) && count($item) === 2 && is_string($item[0]) && is_string($item[1]) && $sortableColumns->contains($item[0]);
            })->map(function ($item) {
                $item[1] = strtolower($item[1]);
                return $item;
            })->filter(function ($item) {
                return in_array($item[1], ['asc', 'desc']);
            });
            $this->order = $order->toArray();
        }
        return $this->order;
    }

    /**
     * @param string|array $method
     *
     * @return void
     */
    public function rejectQuery($method)
    {
        $this->queries = $this->queries->reject(function ($query) use ($method) {
            if (is_callable($method)) {
                return call_user_func($method, $query);
            }
            return in_array($query['method'], (array)$method, true);
        });
    }

    /**
     * Reset orderBy query.
     *
     * @return void
     */
    public function resetOrderBy()
    {
        $this->rejectQuery(['orderBy', 'orderByDesc']);
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        return $this->addQuery($method, $arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return $this
     */
    public function addQuery($method, array $arguments = [])
    {
        $this->queries->push([
            'method' => $method,
            'arguments' => $arguments,
        ]);
        return $this;
    }

}
