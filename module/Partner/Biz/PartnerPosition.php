<?php


namespace Module\Partner\Biz;


class PartnerPosition extends AbstractPartnerPositionBiz
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
