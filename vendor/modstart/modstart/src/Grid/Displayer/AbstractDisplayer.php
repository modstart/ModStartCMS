<?php


namespace ModStart\Grid\Displayer;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;
use ModStart\Grid\Grid;
use ModStart\Support\Concern\HasFluentAttribute;

abstract class AbstractDisplayer implements Renderable
{
    use HasFluentAttribute;

    /**
     * @var array
     */
    protected static $css = [];

    /**
     * @var array
     */
    protected static $js = [];

    /**
     * @var Grid
     */
    protected $grid;

    protected $fluentAttributes = [];

    /**
     * ItemOperate constructor.
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    abstract public function render();

    public function __call($method, $arguments)
    {
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        throw new \Exception('AbstractDisplayer __call error : ' . $method . ' ' . join(',', $this->fluentAttributes));
    }
}