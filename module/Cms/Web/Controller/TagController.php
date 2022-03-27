<?php


namespace Module\Cms\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Util\CmsContentUtil;

class TagController extends ModuleBaseController
{
    public function index($tag)
    {
        if (empty($tag)) {
            return Response::redirect(modstart_web_url(''));
        }
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize('pageSize');
        $option = [];
        $option['search'][] = ['tags' => ['like' => "%:$tag:%"]];
        $paginateData = CmsContentUtil::paginate($page, $pageSize, $option);
        $viewData = [];
        $viewData['tag'] = $tag;
        $viewData['records'] = $paginateData['records'];
        $viewData['total'] = $paginateData['total'];
        $viewData['pageHtml'] = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, '?page={page}');
        return $this->view('cms.tag.index', $viewData);
    }
}
