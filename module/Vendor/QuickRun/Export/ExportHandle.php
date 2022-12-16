<?php


namespace Module\Vendor\QuickRun\Export;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;

class ExportHandle
{
    private $data = [
        'pageTitle' => '导出数据',
        'defaultExportName' => 'Export',
        'headTitles' => [],
    ];

    private $fetchCallback;

    public function withPageTitle($pageTitle)
    {
        $this->data['pageTitle'] = $pageTitle;
        return $this;
    }

    public function withDefaultExportName($defaultExportName)
    {
        $this->data['defaultExportName'] = $defaultExportName;
        return $this;
    }

    public function withHeadTitles($headTitles)
    {
        $this->data['headTitles'] = $headTitles;
        return $this;
    }

    public function handleFetch($callback)
    {
        $this->fetchCallback = $callback;
        return $this;
    }

    public function performExcel()
    {
        return $this->perform('xlsx', 'excel');
    }

    private function perform($ext, $view)
    {
        $pageTitle = $this->data['pageTitle'];
        $defaultExportName = $this->data['defaultExportName'];
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize(null, null, null, 100);
        $search = $input->getJsonAsInput('_param')->getArray('search');
        $exportName = $input->getTrimString('exportName', $defaultExportName);

        if (Request::isPost()) {
            BizException::throwsIfEmpty('导出文件名为空', $exportName);
            $paginateData = call_user_func_array($this->fetchCallback, [$page, $pageSize, $search, []]);
            $data = [];
            $data['code'] = 0;
            $data['list'] = $paginateData['list'];
            $data['total'] = $paginateData['total'];
            $data['finished'] = count($paginateData['list']) != $pageSize;
            $data['exportName'] = $exportName . '.' . $ext;
            $data['exportHeadTitles'] = $this->data['headTitles'];
            return Response::generateSuccessData($data);
        }

        $paginateData = call_user_func_array($this->fetchCallback, [1, 1, $search, []]);
        return view('module::Vendor.View.quickRun.export.' . $view, [
            'pageTitle' => $pageTitle,
            'exportName' => $exportName,
            'total' => $paginateData['total'],
        ]);
    }
}
