<?php

namespace Module\AigcBase\Biz;

abstract class AbstractAigcAppProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function icon();

    abstract public function url();

    public function order()
    {
        return 999;
    }

    public function image()
    {
    }
}
