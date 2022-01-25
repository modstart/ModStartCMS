<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Util\CmsTemplateUtil;

class DetailController extends ModuleBaseController
{
    public function index(\Module\Cms\Api\Controller\DetailController $api,
                          $id = 0)
    {
        InputPackage::mergeToInput('id', $id);
        $viewData = Response::tryGetData($api->index());
        $view = $viewData['view'];
        return $this->view('cms.detail.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}
