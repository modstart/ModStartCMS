<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsModelUtil;

class BaseCatController extends ModuleBaseController
{
    protected function setup($id = 0)
    {
        $input = InputPackage::buildFromInput();
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
        $model = CmsModelUtil::get($cat['modelId']);
        BizException::throwsIfEmpty('模型不存在', $model);
        return [
            'cat' => $cat,
            'catRoot' => $catRoot,
            'catChildren' => $catChildren,
            'catRootChildren' => $catRootChildren,
            'catChain' => $catChain,
        ];
    }

    protected function getView($data, $key)
    {
        $cat = $data['cat'];
        $model = $cat['_model'];
        $view = $cat[$key];
        if (empty($view)) {
            $view = $model[$key];
        }
        BizException::throwsIfEmpty('模板未找到', $view);
        return $view;
    }
}