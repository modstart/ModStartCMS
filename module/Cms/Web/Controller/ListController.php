<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\PageHtmlUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsTemplateUtil;

class ListController extends BaseCatController
{
    public function index($id = 0)
    {
        $data = parent::setup($id);
        $view = $this->getView($data, 'listTemplate');
        $cat = $data['cat'];

        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize('pageSize');
        $paginateData = CmsContentUtil::paginateCat($cat['id'], $page, $pageSize);

        $viewData = $data;
        $viewData['records'] = $paginateData['records'];
        $viewData['pageHtml'] = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, '?page={page}');
        return $this->view('cms.list.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}
