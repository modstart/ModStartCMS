<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Exception\BizException;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsTemplateUtil;

class DetailController extends ModuleBaseController
{
    public function index($id = 0)
    {
        if (is_numeric($id)) {
            $data = CmsContentUtil::get($id);
        } else {
            $data = CmsContentUtil::getByAlias($id);
        }
        $cat = CmsCatUtil::get($data['record']['catId']);
        $catChain = CmsCatUtil::chain($cat['id']);
        $catRoot = CmsCatUtil::root($cat['id']);
        $catRootChildren = CmsCatUtil::children($catRoot['id']);
        $view = $cat['detailTemplate'];
        if (empty($view)) {
            $view = $data['model']['detailTemplate'];
        }
        BizException::throwsIfEmpty('模板未找到', $view);
        $viewData = [];
        $viewData['record'] = $data['record'];
        $viewData['cat'] = $cat;
        $viewData['catRoot'] = $catRoot;
        $viewData['catChain'] = $catChain;
        $viewData['catRootChildren'] = $catRootChildren;
        $viewData['model'] = $data['model'];

        // return $viewData;

        return $this->view('cms.detail.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}
