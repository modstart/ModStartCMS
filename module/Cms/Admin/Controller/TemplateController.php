<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use Module\Cms\Util\CmsTemplateUtil;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

class TemplateController extends Controller
{
    public function index()
    {
        $viewData = [];
        $viewData['template'] = modstart_config()->getWithEnv('siteTemplate', 'default');
        $viewData['provider'] = SiteTemplateProvider::get($viewData['template']);
        $viewData['templateRoot'] = CmsTemplateUtil::templateRoot();
        return view('module::Cms.View.admin.template.index', $viewData);
    }
}
