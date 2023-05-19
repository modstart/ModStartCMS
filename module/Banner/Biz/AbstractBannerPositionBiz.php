<?php


namespace Module\Banner\Biz;


abstract class AbstractBannerPositionBiz
{
    abstract public function name();

    abstract public function title();

    public function remark()
    {
        return null;
    }
}
