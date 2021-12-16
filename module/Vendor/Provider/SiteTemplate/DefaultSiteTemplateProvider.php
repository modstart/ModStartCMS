<?php


namespace Module\Vendor\Provider\SiteTemplate;


/**
 * Class DefaultSiteTemplateProvider
 * @package Module\Vendor\Provider\SiteTemplate
 * @since 1.5.0
 */
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
