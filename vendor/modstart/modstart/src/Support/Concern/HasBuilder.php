<?php


namespace ModStart\Support\Concern;


trait HasBuilder
{
    /**
     * @var \Closure
     */
    private $builder;

    /**
     * @param $builder
     * @return $this
     */
    public function builder($builder)
    {
        $this->builder = $builder;
        return $this;
    }

    private function runBuilder()
    {
        if ($this->builder) {
            call_user_func($this->builder, $this);
        }
    }
}