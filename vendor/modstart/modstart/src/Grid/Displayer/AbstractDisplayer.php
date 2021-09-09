<?php


namespace ModStart\Grid\Displayer;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;
use ModStart\Grid\Grid;
use ModStart\Support\Concern\HasFluentAttribute;

abstract class AbstractDisplayer implements Renderable
{
    use HasFluentAttribute;

    
    protected static $css = [];

    
    protected static $js = [];

    
    protected $grid;

    protected $fluentAttributes = [];

    
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