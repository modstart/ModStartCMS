<?php


namespace Module\Vendor\Provider\HomePage;


class QuickHomePageProvider extends AbstractHomePageProvider
{
    protected $type;
    protected $title;
    protected $action;

    public static function make($title, $action, $type = [self::TYPE_PC, self::TYPE_MOBILE])
    {
        $o = new static();
        $o->type = $type;
        $o->title = $title;
        $o->action = $action;
        return $o;
    }

    public function type()
    {
        return $this->type;
    }


    public function title()
    {
        return $this->title;
    }

    public function action()
    {
        return $this->action;
    }


}
