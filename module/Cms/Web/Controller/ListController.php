<?php


namespace Module\Cms\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\PageHtmlUtil;
use Module\Cms\Api\Controller\BaseCatController;
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
        if (empty($cat['pageSize']) || $cat['pageSize'] < 0) {
            $cat['pageSize'] = 12;
        }
        $pageSize = $input->getPageSize('pageSize', null, null, $cat['pageSize']);
        $paginateData = CmsContentUtil::paginateCat($cat['id'], $page, $pageSize);

        $viewData = $data;
        $viewData['page'] = $page;
        $viewData['pageSize'] = $pageSize;
        $viewData['records'] = $paginateData['records'];
        $pageTemplate = '?page={page}';
        if (!empty($cat['pageFullUrl'])) {
            $pageTemplate = modstart_web_url($cat['pageFullUrl']);
        }
        $viewData['pageNextUrl'] = PageHtmlUtil::nextPageUrl($paginateData['total'], $pageSize, $page, $pageTemplate);
        $viewData['pagePrevUrl'] = PageHtmlUtil::prevPageUrl($paginateData['total'], $pageSize, $page, $pageTemplate);
        $viewData['pageHtml'] = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, $pageTemplate);
        // return $viewData;
        return $this->view('cms.list.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}
