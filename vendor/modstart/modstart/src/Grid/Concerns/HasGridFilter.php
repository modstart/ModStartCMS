<?php


namespace ModStart\Grid\Concerns;


use ModStart\Grid\GridFilter;

trait HasGridFilter
{
    
    protected $gridFilter;

    private function setupGridFilter()
    {
        $this->gridFilter = new GridFilter($this->model);
    }

    
    public function getGridFilter()
    {
        return $this->gridFilter;
    }


}
