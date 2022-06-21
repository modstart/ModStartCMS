<?php


namespace Module\Cms\Api\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Cms\Util\CmsContentUtil;

/**
 * @Api 通用CMS
 */
class PageController extends BaseCatController
{
    /**
     * @Api 单页-获取信息
     * @ApiBodyParam cat string 栏目标识（ID、名称）
     */
    public function index()
    {
        $input = InputPackage::buildFromInput();
        $catId = $input->getTrimString('cat');
        $data = parent::setup($catId);
        $view = $this->getView($data, 'pageTemplate');
        $cat = $data['cat'];
        $viewData = $data;
        $records = CmsContentUtil::allCat($cat['id']);
        $viewData['record'] = isset($records[0]) ? $records[0] : null;
        $viewData['records'] = $records;
        $viewData['view'] = $view;
        return Response::generateSuccessData($viewData);
    }
}
