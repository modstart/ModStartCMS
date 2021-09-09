<?php

namespace ModStart\Grid\Filter;

class Where extends AbstractFilter
{
    
    protected $where;

    
    public $input;

    
    public function __construct(\Closure $query, $label)
    {
        $this->where = $query;

        $this->label = $this->formatLabel($label);
        $this->column = static::getQueryHash($query, $this->label);
        $this->id = $this->formatId($this->column);

        $this->setupField();
    }

    
    public static function getQueryHash(\Closure $closure, $label = '')
    {
        $reflection = new \ReflectionFunction($closure);

        return md5($reflection->getFileName() . $reflection->getStartLine() . $reflection->getEndLine() . $label);
    }

    
    public function condition($search)
    {
        $value = array_get($search, static::getQueryHash($this->where, $this->label));

        if (is_array($value)) {
            $value = array_filter($value);
        }

        if (is_null($value) || empty($value)) {
            return;
        }

        $this->input = $this->value = $value;

        return $this->buildCondition($this->where->bindTo($this));
    }
}
