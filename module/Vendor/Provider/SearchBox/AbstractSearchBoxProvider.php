<?php


namespace Module\Vendor\Provider\SearchBox;


abstract class AbstractSearchBoxProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function url();

    public function order()
    {
        return 1000;
    }
}
