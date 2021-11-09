<?php


namespace Module\Vendor\Provider\SiteTemplate;



abstract class AbstractSiteTemplateProvider
{
    abstract public function name();

    abstract public function title();

    public function root()
    {
        return null;
    }
}
