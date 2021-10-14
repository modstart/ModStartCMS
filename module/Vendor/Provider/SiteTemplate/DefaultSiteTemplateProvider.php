<?php


namespace Module\Vendor\Provider\SiteTemplate;



class DefaultSiteTemplateProvider extends AbstractSiteTemplateProvider
{
    public function title()
    {
        return L('Default');
    }

    public function name()
    {
        return 'default';
    }

}
