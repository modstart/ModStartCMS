<?php


namespace Module\Vendor\Provider\SiteTemplate;


/**
 * Class AbstractSiteTemplateProvider
 * @package Module\Vendor\Provider\SiteTemplate
 * @since 1.5.0
 */
abstract class AbstractSiteTemplateProvider
{
    abstract public function name();

    abstract public function title();

    public function root()
    {
        return null;
    }
}
