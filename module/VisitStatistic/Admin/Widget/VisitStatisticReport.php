<?php

namespace Module\VisitStatistic\Admin\Widget;

use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\ModStart;
use ModStart\Widget\AbstractRawWidget;
use ModStart\Widget\Traits\HasRequestTrait;
use ModStart\Widget\Traits\HasVueFileTrait;
use Module\VisitStatistic\Util\VisitStatisticUtil;

class VisitStatisticReport extends AbstractRawWidget
{
    use HasRequestTrait;
    use HasVueFileTrait;

    public function request()
    {
        $input = InputPackage::buildFromInput();
        $data = VisitStatisticUtil::report(
            $input->getDate('start'),
            $input->getDate('end')
        );
        return Response::generateSuccessData($data);
    }

    public function contentRenderBefore()
    {
        ModStart::js('asset/vendor/echarts/echarts.all.js');
    }

    public function permit()
    {
        return AdminPermission::permit('\Module\VisitStatistic\Admin\Controller\VisitStatisticReportController@index');
    }


}
