<?php


namespace Module\Cms\Core;


use Module\TagManager\Biz\AbstractTagManagerBiz;

class CmsTagManagerBiz extends AbstractTagManagerBiz
{
    public function name()
    {
        return 'cms';
    }

    public function title()
    {
        return '通用CMS';
    }

    public function searchUrl($tag)
    {
        return modstart_web_url('tag/' . urlencode($tag));
    }

}
