<?php


namespace Module\Partner\Biz;


class QuickPartnerPositionBiz extends AbstractPartnerPositionBiz
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
