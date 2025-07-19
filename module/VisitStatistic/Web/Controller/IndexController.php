<?php

namespace Module\VisitStatistic\Web\Controller;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\AgentUtil;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Module\ModuleBaseController;
use Module\VisitStatistic\Model\VisitStatisticItem;
use Module\VisitStatistic\Type\VisitStatisticDevice;

class IndexController extends ModuleBaseController
{
    public function index()
    {
        if (modstart_config('VisitStatistic_TickEnable', false)) {
            $input = InputPackage::buildFromInput();
            $url = $input->getTrimString('url', '');
            if (empty($url)) {
                $url = Request::headerReferer();
            }
            $url = parse_url($url, PHP_URL_PATH);
            $record = [];
            $record['url'] = $url;
            $record['ip'] = Request::ip();
            $record['device'] = VisitStatisticDevice::current();
            $record['ua'] = StrUtil::mbLimit(AgentUtil::getUserAgent(), 200);
            //LogUtil::info('VisitStatistic.tick', $record);
            ModelUtil::insert(VisitStatisticItem::class, $record);
        }
        return Response::raw(
            '',
            [
                'Content-Type' => 'image/gif',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

}
