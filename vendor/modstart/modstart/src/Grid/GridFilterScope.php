<?php

namespace ModStart\Grid;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * @mixin Builder
 */
class GridFilterScope implements Renderable
{
    /**
     * @var GridFilter
     */
    protected $filter;

    /**
     * @var string
     */
    public $key = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var Collection
     */
    protected $queries;

    /**
     * Scope constructor.
     *
     * @param GridFilter $filter
     * @param string $key
     * @param string $label
     */
    public function __construct(GridFilter $filter, $key = 'fixed', $label = '')
    {
        $this->filter = $filter;
        $this->key = $key;
        $this->label = $label ? $label : $key;
        $this->queries = new Collection();
    }

    /**
     * Get model query conditions.
     *
     * @return array
     */
    public function condition()
    {
        return $this->queries->map(function ($query) {
            return [$query['method'] => $query['arguments']];
        })->toArray();
    }

    /**
     * @return string
     */
    public function render()
    {
        return '';
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
