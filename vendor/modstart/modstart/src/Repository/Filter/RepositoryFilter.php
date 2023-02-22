<?php

namespace ModStart\Repository\Filter;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * @mixin Builder
 */
class RepositoryFilter
{

    /**
     * @var Collection
     */
    private $queries;

    /**
     * RepositoryFilter constructor.
     */
    public function __construct()
    {
        $this->queries = new Collection();
    }

    /**
     * @return Collection
     */
    public function clear()
    {
        $this->queries = new Collection();
        return $this;
    }

    public function executeQueries(&$query)
    {
        $this->queries->each(function ($value) use (&$query) {
            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ? $value['arguments'] : []);
        });
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->queries->push([
            'method' => $method,
            'arguments' => $arguments,
        ]);
        return $this;
    }
}
