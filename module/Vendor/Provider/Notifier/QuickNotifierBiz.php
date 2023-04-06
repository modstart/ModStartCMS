<?php


namespace Module\Vendor\Provider\Notifier;


class QuickNotifierBiz extends AbstractNotifierBiz
{
    private $name;
    private $title;

    public function __construct($name, $title)
    {
        $this->name = $name;
        $this->title = $title;
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
