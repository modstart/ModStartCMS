<?php


namespace ModStart\Grid\Concerns;


use ModStart\Grid\GridFilter;

trait HasGridFilter
{
    /**
     * @var array Grid筛选条件Join表
     */
    protected $gridFilterJoins = [];

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

    public function gridFilterJoins()
    {
        return $this->gridFilterJoins;
    }

    /**
     * @param $mode = left|right|inner
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     * @return $this
     */
    public function gridFilterJoinAdd($mode, $table, $first, $operator = null, $second = null)
    {
        $this->gridFilterJoins[] = [
            $mode, $table, $first, $operator, $second
        ];
        return $this;
    }

}
