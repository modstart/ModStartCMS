<?php


namespace ModStart\Repository;

class EmptyItem extends \stdClass
{
    private $attributes = [];

    
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
}

class RepositoryUtil
{
    public static function itemsFromArray(array $array)
    {
        return collect($array)->map(function ($o) {
            return (object)$o;
        });
    }

    
    public static function itemFromArray($item)
    {
        return (object)$item;
    }

    public static function makeItem($initValue = [])
    {
        return new EmptyItem($initValue);
    }

    public static function makeItems($itemOrItems)
    {
        if ($itemOrItems instanceof \Illuminate\Support\Collection) {
            return $itemOrItems;
        }
        return collect([$itemOrItems]);
    }
}
