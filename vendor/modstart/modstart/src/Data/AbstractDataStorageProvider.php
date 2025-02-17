<?php

namespace ModStart\Data;

abstract class AbstractDataStorageProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function enable();

    public function uploadScript($param = [])
    {

    }
}
