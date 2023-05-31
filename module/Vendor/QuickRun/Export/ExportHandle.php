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
        'customHeadTitle' => false,
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

    public function withCustomHeadTitle($enable)
    {
        $this->data['customHeadTitle'] = $enable;
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

    public function performCsv()
    {
        return $this->perform('xlsx', 'csv');
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
        $pageTitle = $this->data['pageTitle'];
        $defaultExportName = $this->data['defaultExportName'];
        $headTitles = $this->data['headTitles'];
        $customHeadTitle = $this->data['customHeadTitle'];
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize(null, null, null, 100);
        $search = $input->getJsonAsInput('_param')->getArray('search');
        $exportName = $input->getTrimString('exportName', $defaultExportName);
        $format = $input->getTrimString('format', $ext);
        $checkedHeadTitles = $input->getJson('checkedHeadTitles');

        if (Request::isPost()) {
            BizException::throwsIfEmpty('导出文件名为空', $exportName);
            BizException::throwsIfEmpty('请选择导出列', $checkedHeadTitles);
            $paginateData = call_user_func_array($this->fetchCallback, [$page, $pageSize, $search, []]);
            if ($customHeadTitle) {
                $list = [];
                foreach ($paginateData['list'] as $v) {
                    $one = [];
                    foreach ($v as $vi => $vv) {
                        if (in_array($vi, $checkedHeadTitles)) {
                            $one[] = $vv;
                        }
                    }
                    $list[] = $one;
                }
                $exportHeadTitles = [];
                foreach ($this->data['headTitles'] as $i => $v) {
                    if (in_array($i, $checkedHeadTitles)) {
                        $exportHeadTitles[] = $v;
                    }
                }
            } else {
                $list = $paginateData['list'];
                $exportHeadTitles = $this->data['headTitles'];
            }
            $data = [];
            $data['code'] = 0;
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['finished'] = count($paginateData['list']) != $pageSize;
            $data['exportName'] = $exportName . '.' . $format;
            $data['exportHeadTitles'] = $exportHeadTitles;
            return Response::generateSuccessData($data);
        }

        $paginateData = call_user_func_array($this->fetchCallback, [1, 1, $search, []]);
        return view('module::Vendor.View.quickRun.export.' . $view, [
            'pageTitle' => $pageTitle,
            'exportName' => $exportName,
            'total' => $paginateData['total'],
            'formats' => $param['formats'],
            'customHeadTitle' => $customHeadTitle,
            'headTitles' => $headTitles,
        ]);
    }
}
