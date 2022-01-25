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
        return $this->view('cms.page.' . CmsTemplateUtil::toBladeView($viewData['view']), $viewData);
    }
}