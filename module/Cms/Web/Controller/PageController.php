<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Cms\Api\Controller\BaseCatController;
use Module\Cms\Util\CmsTemplateUtil;

class PageController extends BaseCatController
{
    public function index(\Module\Cms\Api\Controller\PageController $api,
                          $id = 0)
    {
        InputPackage::mergeToInput('id', $id);
        $viewData = Response::tryGetData($api->index());
        $cat = $viewData['cat'];
        $viewData['pageTitle'] = ($cat['seoTitle'] ? $cat['seoTitle'] : $cat['title']) . ' | ' . modstart_config('siteName');
        $viewData['pageKeywords'] = $cat['seoKeywords'] ? $cat['seoKeywords'] : $cat['title'];
        $viewData['pageDescription'] = $cat['seoDescription'] ? $cat['seoDescription'] : $cat['title'];
        return $this->view('cms.page.' . CmsTemplateUtil::toBladeView($viewData['view']), $viewData);
    }
}
