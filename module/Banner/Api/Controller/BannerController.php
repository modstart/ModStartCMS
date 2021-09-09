<?php


namespace Module\Banner\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Banner\Util\BannerUtil;

class BannerController extends Controller
{
    public function get()
    {
        $input = InputPackage::buildFromInput();
        $position = $input->getTrimString('position');
        $list = BannerUtil::listByPositionWithCache($position);
        foreach ($list as $k => $v) {
            $list[$k]['image'] = AssetsUtil::fixFull($v['image']);
        }
        return Response::generateSuccessData($list);
    }
}