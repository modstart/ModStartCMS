<?php


namespace ModStart\Grid\Concerns;


/**
 * Trait HasPaginator
 * @package ModStart\Grid\Concerns
 */
trait HasPaginator
{
    public function enablePagination($enable = false)
    {
        $this->model->usePaginate($enable);
        $this->setFluentAttribute('enablePagination', $enable);
        return $this;
    }
}
