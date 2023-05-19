<?php


namespace Module\Banner\Biz;


class QuickBannerPositionBiz extends AbstractBannerPositionBiz
{
    protected $name;
    protected $title;
    protected $remark;

    public static function make($name, $title, $remark = null)
    {
        $o = new static();
        $o->name = $name;
        $o->title = $title;
        $o->remark = $remark;
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

    public function remark()
    {
        return $this->remark;
    }

}
