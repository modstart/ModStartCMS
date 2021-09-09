<?php

namespace ModStart\Grid;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;


class GridFilterScope implements Renderable
{
    
    protected $filter;

    
    public $key = '';

    
    protected $label = '';

    
    protected $queries;

    
    public function __construct(GridFilter $filter, $key = 'fixed', $label = '')
    {
        $this->filter = $filter;
        $this->key = $key;
        $this->label = $label ? $label : $key;
        $this->queries = new Collection();
    }

    
    public function condition()
    {
        return $this->queries->map(function ($query) {
            return [$query['method'] => $query['arguments']];
        })->toArray();
    }

    
    public function render()
    {
        return '';
    }

    
    public function __call($method, $arguments)
    {
        $this->queries->push([
            'method' => $method,
            'arguments' => $arguments,
        ]);
        return $this;
    }
}
