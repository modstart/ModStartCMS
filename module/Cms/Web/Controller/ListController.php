<?php


namespace Module\Cms\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\PageHtmlUtil;
use Module\Cms\Api\Controller\BaseCatController;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsMemberPermitUtil;
use Module\Cms\Util\CmsTemplateUtil;

class ListController extends BaseCatController
{
    public function index($id = 0)
    {
        $data = parent::setup($id);
        $view = $this->getView($data, 'listTemplate');
        $cat = $data['cat'];
        $model = $data['model'];

        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        if (empty($cat['pageSize']) || $cat['pageSize'] < 0) {
            $cat['pageSize'] = 12;
        }
        $pageSize = $input->getPageSize('pageSize', null, null, $cat['pageSize']);

        $option = [];
        $option = CmsContentUtil::buildFilter($option, $model);
        $paginateData = CmsContentUtil::paginateCat($cat['id'], $page, $pageSize, $option);
        CmsContentUtil::mergeRecordsData($paginateData['records'], [
            'canVisit' => CmsMemberPermitUtil::canVisitCat($cat),
        ]);

        $viewData = $data;
        $viewData['page'] = $page;
        $viewData['pageSize'] = $pageSize;
        $viewData['records'] = $paginateData['records'];
        $viewData['total'] = $paginateData['total'];
        $pageTemplate = '?page={page}';
        if (!empty($cat['pageFullUrl'])) {
            $pageTemplate = modstart_web_url($cat['pageFullUrl']);
        }
        $viewData['pageTemplate'] = $pageTemplate;
        $viewData['pageNextUrl'] = PageHtmlUtil::nextPageUrl($paginateData['total'], $pageSize, $page, $pageTemplate);
        $viewData['pagePrevUrl'] = PageHtmlUtil::prevPageUrl($paginateData['total'], $pageSize, $page, $pageTemplate);
        $viewData['pageHtml'] = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, $pageTemplate);

        $viewData['pageTitle'] = ($cat['seoTitle'] ? $cat['seoTitle'] : $cat['title']) . ' | ' . modstart_config('siteName');
        $viewData['pageKeywords'] = $cat['seoKeywords'] ? $cat['seoKeywords'] : $cat['title'];
        $viewData['pageDescription'] = $cat['seoDescription'] ? $cat['seoDescription'] : $cat['title'];

        // return $viewData;
        return $this->view('cms.list.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}
