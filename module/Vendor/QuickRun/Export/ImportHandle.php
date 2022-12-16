<?php


namespace Module\Vendor\QuickRun\Export;


use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\HtmlUtil;

class ImportHandle
{
    private $data = [
        'frameView' => 'modstart::admin.dialogFrame',
        'pageTitle' => '导入数据',
        'pageDescription' => '',
        'templateName' => 'Template',
        'templateData' => [],
        'headTitles' => [],
    ];

    private $importCallback;

    public function withDialog($enable = true)
    {
        if ($enable) {
            $this->data['frameView'] = 'modstart::admin.dialogFrame';
        } else {
            $this->data['frameView'] = 'modstart::admin.frame';
        }
        return $this;
    }

    public function withPageTitle($pageTitle)
    {
        $this->data['pageTitle'] = $pageTitle;
        return $this;
    }

    public function withPageDescription($pageDescription, $isHtml = false)
    {
        if (!$isHtml) {
            $pageDescription = HtmlUtil::text2html($pageDescription);
        }
        $this->data['pageDescription'] = $pageDescription;
        return $this;
    }

    public function withTemplateName($templateName)
    {
        $this->data['templateName'] = $templateName;
        return $this;
    }

    public function withTemplateData($templateData)
    {
        $this->data['templateData'] = $templateData;
        return $this;
    }

    public function withHeadTitles($headTitles)
    {
        $this->data['headTitles'] = $headTitles;
        return $this;
    }

    public function handleImport($callback)
    {
        $this->importCallback = $callback;
        return $this;
    }

    public function performExcel()
    {
        return $this->perform('xlsx', 'excel');
    }

    private function perform($ext, $view)
    {
        $input = InputPackage::buildFromInput();
        if (Request::isPost()) {
            $data = $input->getJson('data');
            AdminPermission::demoCheck();
            return call_user_func_array($this->importCallback, [$data, []]);
        }
        return view('module::Vendor.View.quickRun.import.' . $view, array_merge($this->data, [

        ]));
    }
}
