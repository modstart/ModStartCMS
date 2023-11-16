<?php


namespace Module\Cms\Web\Controller;

use ModStart\Core\Input\InputPackage;
use Module\Cms\Api\Controller\BaseCatController;
use Module\Cms\Util\CmsTemplateUtil;

class FormController extends BaseCatController
{
    public function index($id = 0)
    {
        $data = parent::setup($id);
        $cat = $data['cat'];
        $view = $this->getView($data, 'formTemplate');
        $viewData = $data;
        $viewData['model'] = $data['cat']['_model'];
        $viewData['pageTitle'] = ($cat['seoTitle'] ? $cat['seoTitle'] : $cat['title']) . ' | ' . modstart_config('siteName');
        $viewData['pageKeywords'] = $cat['seoKeywords'] ? $cat['seoKeywords'] : $cat['title'];
        $viewData['pageDescription'] = $cat['seoDescription'] ? $cat['seoDescription'] : $cat['title'];
        return $this->view('cms.form.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }

    public function submit(\Module\Cms\Api\Controller\FormController $api, $cat = null)
    {
        InputPackage::mergeToInput('cat', $cat);
        return $api->submit();
    }
}
