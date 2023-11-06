<?php


namespace Module\Vendor\Provider\SiteTemplate;


class QuickSiteTemplateProvider extends AbstractSiteTemplateProvider
{
    private $name;
    private $title;
    private $root;

    public static function make($name, $title, $root = null)
    {
        $ins = new static();
        $ins->name = $name;
        $ins->title = $title;
        $ins->root = $root;
        return $ins;
    }

    public function title()
    {
        return $this->title;
    }

    public function name()
    {
        return $this->name;
    }

    public function root()
    {
        return $this->root;
    }


}
