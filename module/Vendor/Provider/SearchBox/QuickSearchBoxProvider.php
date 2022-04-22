<?php


namespace Module\Vendor\Provider\SearchBox;


class QuickSearchBoxProvider extends AbstractSearchBoxProvider
{
    protected $name;
    protected $title;
    protected $url;
    protected $order;

    public static function make($name, $title, $url, $order = 1000)
    {
        $o = new static();
        $o->name = $name;
        $o->title = $title;
        $o->url = $url;
        $o->order = $order;
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

    public function url()
    {
        return $this->url;
    }

    public function order()
    {
        return $this->order;
    }

}
