<?php


namespace Module\Cms\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Util\CmsContentUtil;
use Module\WordSpliter\Util\WordSpliterUtil;

class SearchController extends ModuleBaseController
{
    public function index()
    {
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize('pageSize');
        $keywords = $input->getTrimString('keywords');
        $option = [];
        if (!empty($keywords)) {
            if (modstart_module_enabled('WordSpliter')) {
                $keywordsList = WordSpliterUtil::cut($keywords);
                $option['search'][] = ['__exp' => 'or', 'title' => ['likes' => $keywordsList], 'tags' => ['like' => ":$keywords:"]];
            } else {
                $option['search'][] = ['__exp' => 'or', 'title' => ['like' => $keywords], 'tags' => ['like' => ":$keywords:"]];
            }
        }
        $paginateData = CmsContentUtil::paginate($page, $pageSize, $option);
        $viewData = [];
        $viewData['keywords'] = $keywords;
        $viewData['records'] = $paginateData['records'];
        $viewData['total'] = $paginateData['total'];
        $viewData['pageHtml'] = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, '?' . Request::mergeQueries(['page' => ['{page}']]));
        return $this->view('cms.search.index', $viewData);
    }
}
