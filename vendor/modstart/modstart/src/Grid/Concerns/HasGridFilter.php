<?php


namespace ModStart\Grid\Concerns;


use ModStart\Grid\GridFilter;

trait HasGridFilter
{
    /**
     * @var GridFilter
     */
    protected $gridFilter;

    private function setupGridFilter()
    {
        $this->gridFilter = new GridFilter($this->model);
    }

    /**
     * @return GridFilter
     */
    public function getGridFilter()
    {
        return $this->gridFilter;
    }


}
