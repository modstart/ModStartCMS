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
        $view = $this->getView($data, 'formTemplate');
        $viewData = $data;
        $viewData['model'] = $data['cat']['_model'];
        return $this->view('cms.form.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }

    public function submit(\Module\Cms\Api\Controller\FormController $api, $cat = null)
    {
        InputPackage::mergeToInput('cat', $cat);
        return $api->submit();
    }
}