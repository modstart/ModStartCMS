<?php


namespace Module\Vendor\Provider\SiteUrl;


abstract class AbstractSiteUrlProvider
{
    abstract public function update($url, $title = '', $param = []);
}
