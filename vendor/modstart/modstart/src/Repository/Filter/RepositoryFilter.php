<?php

namespace ModStart\Repository\Filter;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;


class RepositoryFilter
{

    
    private $queries;

    
    public function __construct()
    {
        $this->queries = new Collection();
    }

    public function executeQueries(&$query)
    {
        $this->queries->each(function ($value) use (&$query) {
            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ? $value['arguments'] : []);
        });
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
