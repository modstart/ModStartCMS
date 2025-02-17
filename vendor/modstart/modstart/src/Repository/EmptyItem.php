<?php


namespace ModStart\Repository;


class EmptyItem extends \stdClass
{
    private $attributes = [];

    /**
     * EmptyItem constructor.
     */
    public function __construct($initValue = [])
    {
        $this->attributes = array_merge($this->attributes, $initValue);
    }


    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}
