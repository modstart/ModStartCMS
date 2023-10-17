<?php


namespace ModStart\Data\Support;


abstract class AbstractFileManager
{
    abstract public function name();

    abstract public function title();

    public function getCategoryTree($category, $param = [])
    {
        return null;
    }

    public function listExecute($category, $categoryId, $param = [])
    {
        return null;
    }
}
