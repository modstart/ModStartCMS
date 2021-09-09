<?php

namespace ModStart\Grid;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use ModStart\Field\AbstractField;
use ModStart\Repository\Repository;


class Model
{
    
    private $grid;

    
    private $repository;

    
    private $paginator;

    
    private $queries;

    
    private $order;

    
    private $data;

    
    private $builder;

    
    private $pageSize = 10;

    
    private $pageName = 'page';

    
    private $page;

    
    private $usePaginate = true;

    
    private $pageSizeName = 'pageSize';

    
    private $orderName = 'order';


    
    public function __construct($grid, $repository = null)
    {
        $this->grid = $grid;
        if ($repository) {
            $this->repository = Repository::instance($repository);
        }
        $this->queries = new Collection();
    }

    
    public function repository()
    {
        return $this->repository;
    }

    
    public function getQueries()
    {
        return $this->queries = $this->queries->unique();
    }

    
    public function paginator()
    {
        $this->buildData();
        return $this->paginator;
    }



    
    public function usePaginate($use = true)
    {
        $this->usePaginate = $use;
    }

    
    public function allowPagination()
    {
        return $this->usePaginate;
    }

    
    public function getPageSizeName()
    {
        return $this->pageSizeName;
    }

    
    public function setPageSizeName($name)
    {
        $this->pageSizeName = $name;
        return $this;
    }

    
    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    
    public function setPageName(string $pageName)
    {
        $this->pageName = $pageName;

        return $this;
    }

    
    public function getPageName()
    {
        return $this->pageName;
    }

    
    public function getOrderName()
    {
        return $this->orderName;
    }

    
    public function grid()
    {
        return $this->grid;
    }

    

    
    public function buildData()
    {
        if (is_null($this->data)) {
            $this->setData($this->fetch());
        }
        return $this->data;
    }

    
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
                return $this;
    }

    
    public function addConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            call_user_func_array([$this, key($condition)], current($condition));
        }

        return $this;
    }

    
    private function fetch()
    {
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

    
    private function setPaginator(AbstractPaginator $paginator)
    {
        $this->paginator = $paginator;
        $paginator->setPageName($this->pageName);
    }

    
    public function getPage()
    {
        if (!$this->usePaginate) {
            return;
        }
        return $this->page = $this->repository->getArgument($this->pageName, 1);
    }

    
    public function setPage(int $page)
    {
        $this->page = $page;

        return $this;
    }

    
    public function getPageSize()
    {
        if (!$this->usePaginate) {
            return;
        }
        return $this->repository->getArgument('pageSize', $this->pageSize);
    }

    
    public function findQueryByMethod($method)
    {
        return $this->queries->first(function ($query) use ($method) {
            return $query['method'] == $method;
        });
    }



    
    public function setOrder($order)
    {
        $this->order = $order;
    }

    
    public function getOrder()
    {
        $order = $this->repository->getArgument('order', []);
        $orderDefault = $this->repository->getArgument('orderDefault', []);
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

    
    public function rejectQuery($method)
    {
        $this->queries = $this->queries->reject(function ($query) use ($method) {
            if (is_callable($method)) {
                return call_user_func($method, $query);
            }
            return in_array($query['method'], (array)$method, true);
        });
    }

    
    public function resetOrderBy()
    {
        $this->rejectQuery(['orderBy', 'orderByDesc']);
    }

    
    public function __call($method, $arguments)
    {
        return $this->addQuery($method, $arguments);
    }

    
    public function addQuery($method, array $arguments = [])
    {
        $this->queries->push([
            'method' => $method,
            'arguments' => $arguments,
        ]);
        return $this;
    }
}
