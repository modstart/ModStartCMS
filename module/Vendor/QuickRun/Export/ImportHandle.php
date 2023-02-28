<?php


namespace Module\Vendor\QuickRun\Export;


use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\ArrayPackage;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
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

    public function example(ImportHandle $handle)
    {
        return $handle
            ->withPageTitle('导入/更新商品信息')
            ->withPageDescription('商品编码唯一，根据商品编码更新或新建商品')
            ->withTemplateName('商品信息')
            ->withTemplateData([
                ['XXXX', '测试名称'],
            ])
            ->withHeadTitles([
                '编码', '标题',
            ])
            ->handleImport(function ($data, $param) {
                $package = ArrayPackage::build($data);
                $where = [];
                $where['sn'] = $package->nextTrimString();
                $update = [];
                $update['title'] = $package->nextTrimString();
                ModelUtil::update('xxxx', $where, $update);
                return Response::generateSuccess();
            })
            ->performExcel();
    }

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
        return $this->perform('xlsx', 'common', [
            'formats' => ['xlsx'],
        ]);
    }

    public function performCsv()
    {
        return $this->perform('csv', 'common', [
            'formats' => ['csv'],
        ]);
    }

    public function performCommon($param = [])
    {
        return $this->perform(null, 'common', $param);
    }

    private function perform($ext, $view, $param = [])
    {
        if (null === $ext) {
            $ext = 'xlsx';
        }
        if (!isset($param['formats'])) {
            $param['formats'] = ['xlsx', 'csv'];
        }
        $input = InputPackage::buildFromInput();
        if (Request::isPost()) {
            $data = $input->getJson('data');
            AdminPermission::demoCheck();
            return call_user_func_array($this->importCallback, [$data, []]);
        }
        return view('module::Vendor.View.quickRun.import.' . $view, array_merge($this->data, [
            'ext' => $ext,
            'formats' => $param['formats'],
        ]));
    }
}
