<?php


namespace Module\CmsThemeCorp\Provider;


use Module\Vendor\Provider\SiteTemplate\AbstractSiteTemplateProvider;

class CorpSiteTemplateProvider extends AbstractSiteTemplateProvider
{
    const NAME = 'corp';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return 'CMS商务模板';
    }

    public function root()
    {
        return 'module::CmsThemeCorp.View';
    }

}
