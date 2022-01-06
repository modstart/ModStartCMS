<?php


namespace ModStart\Repository\Filter;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;


/**
 * @mixin Builder
 */
class ScopeFilter
{
    /**
     * @var Collection
     */
    private $queries;

    /**
     * GlobalFilterItem constructor.
     */
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