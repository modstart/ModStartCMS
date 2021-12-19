<?php


namespace Module\Cms\Web\Controller;


use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsTemplateUtil;

class PageController extends BaseCatController
{
    public function index($id = 0)
    {
        $data = parent::setup($id);
        $view = $this->getView($data, 'pageTemplate');
        $cat = $data['cat'];

        $viewData = $data;
        $records = CmsContentUtil::allCat($cat['id']);
        $viewData['record'] = isset($records[0]) ? $records[0] : null;
        $viewData['records'] = $records;

        return $this->view('cms.page.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}