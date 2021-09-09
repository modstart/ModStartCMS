<?php


namespace ModStart\Grid\Concerns;



trait HasPaginator
{
    public function enablePagination($enable = false)
    {
        $this->model->usePaginate($enable);
        $this->setFluentAttribute('enablePagination', $enable);
        return $this;
    }
}
