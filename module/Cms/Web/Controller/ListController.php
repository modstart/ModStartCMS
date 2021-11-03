<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsModelUtil;
use Module\Cms\Util\CmsTemplateUtil;

class ListController extends ModuleBaseController
{
    public function index($id = 0)
    {
        $input = InputPackage::buildFromInput();
        $cat = null;
        if (empty($id)) {
            $cat = CmsCatUtil::getByUrl(Request::path());
        } else {
            $cat = CmsCatUtil::get($id);
        }
        BizException::throwsIfEmpty('分类不存在', $cat);
        $catRoot = CmsCatUtil::root($cat['id']);
        $catChildren = CmsCatUtil::children($cat['id']);
        $catRootChildren = CmsCatUtil::children($catRoot['id']);
        $catChain = CmsCatUtil::chain($cat['id']);
        $page = $input->getPage();
        $pageSize = $input->getPageSize('pageSize');
        if ($pageSize == 10) {
            $pageSize = 12;
        }
        $paginateData = CmsContentUtil::paginateCat($cat['id'], $page, $pageSize);
        $view = $cat['listTemplate'];
        if (empty($view)) {
            $model = CmsModelUtil::get($cat['modelId']);
            BizException::throwsIfEmpty('模型不存在', $model);
            $view = $model['listTemplate'];
        }
        BizException::throwsIfEmpty('模板未找到', $view);
        $viewData = [];
        $viewData['cat'] = $cat;
        $viewData['catChildren'] = $catChildren;
        $viewData['catRoot'] = $catRoot;
        $viewData['catRootChildren'] = $catRootChildren;
        $viewData['catChain'] = $catChain;
        $viewData['records'] = $paginateData['records'];
        $viewData['pageHtml'] = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, '?page={page}');
        return $this->view('cms.list.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}
