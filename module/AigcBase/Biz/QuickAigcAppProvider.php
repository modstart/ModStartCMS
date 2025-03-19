<?php

namespace Module\AigcBase\Biz;

class QuickAigcAppProvider extends AbstractAigcAppProvider
{
    public $name;
    public $title;
    public $url;
    public $icon;
    public $image;
    public $order = 999;

    public function name()
    {
        return $this->name;
    }

    public function title()
    {
        return $this->title;
    }

    public function icon()
    {
        return $this->icon;
    }

    public function url()
    {
        return $this->url;
    }

    public function image()
    {
        return $this->image;
    }

    public function order()
    {
        return $this->order;
    }


}
