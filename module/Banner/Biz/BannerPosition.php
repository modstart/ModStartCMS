<?php


namespace Module\Banner\Biz;


class BannerPosition extends AbstractBannerPositionBiz
{
    private $name;
    private $title;

    public static function make($name, $title)
    {
        $one = new static();
        $one->name = $name;
        $one->title = $title;
        return $one;
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
