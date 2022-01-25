<?php


namespace Module\Banner\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Banner\Util\BannerUtil;

/**
 * Class BannerController
 * @package Module\Banner\Api\Controller
 * @Api 通用轮播
 */
class BannerController extends Controller
{
    /**
     * @return array
     *
     * @Api 获取轮播信息
     * @ApiBodyParam position string 位置信息
     */
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