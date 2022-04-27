<?php


namespace Module\Banner\Biz;


class QuickBannerPositionBiz extends AbstractBannerPositionBiz
{
    protected $name;
    protected $title;

    public static function make($name, $title)
    {
        $o = new static();
        $o->name = $name;
        $o->title = $title;
        return $o;
    }

    public function name()
    {
        return $this->name;
    }

    public function title()
    {
        return $this->title;
    }


}
