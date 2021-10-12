<?php


namespace ModStart\Repository;

use Illuminate\Support\Collection;

class RepositoryUtil
{
    public static function itemsFromArray(array $array)
    {
        return collect($array)->map(function ($o) {
            return (object)$o;
        });
    }

    /**
     * @param array|\stdClass $item
     * @return object
     */
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
        if ($itemOrItems instanceof Collection) {
            return $itemOrItems;
        }
        return collect([$itemOrItems]);
    }
}
