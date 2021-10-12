<?php

namespace ModStart\Grid\Filter;

class Where extends AbstractFilter
{
    /**
     * Query closure.
     *
     * @var \Closure
     */
    protected $where;

    /**
     * Input value from field.
     *
     * @var
     */
    public $input;

    /**
     * Where constructor.
     * @param \Closure $query
     * @param $label
     * @throws \ReflectionException
     */
    public function __construct(\Closure $query, $label)
    {
        $this->where = $query;

        $this->label = $this->formatLabel($label);
        $this->column = static::getQueryHash($query, $this->label);
        $this->id = $this->formatId($this->column);

        $this->setupField();
    }

    /**
     * @param \Closure $closure
     * @param string $label
     * @return string
     * @throws \ReflectionException
     */
    public static function getQueryHash(\Closure $closure, $label = '')
    {
        $reflection = new \ReflectionFunction($closure);

        return md5($reflection->getFileName() . $reflection->getStartLine() . $reflection->getEndLine() . $label);
    }

    /**
     * @param $search
     * @return array|mixed|void|null
     * @throws \ReflectionException
     */
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
