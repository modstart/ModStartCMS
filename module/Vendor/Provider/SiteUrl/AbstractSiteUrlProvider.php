<?php


namespace Module\Vendor\Provider\SiteUrl;


/**
 * 网站链接更新提供者，如sitemap文件生成、搜索引擎自动提交等
 * 链接 更新、删除 时会调用
 */
abstract class AbstractSiteUrlProvider
{
    abstract public function update($url, $title = '', $param = []);

    abstract public function delete($url);
}
