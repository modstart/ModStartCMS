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
 * @Api 物料
 */
class BannerController extends Controller
{
    /**
     * @Api 获取轮播信息
     * @ApiDesc 根据位置获取轮播图列表
     * @ApiMethod post
     * @ApiBodyParam position string required 轮播图位置
     * @ApiResponseData {
     *   "code": 0,
     *   "msg": "",
     *   "data": [
     *     {
     *       "id": 1,
     *       "image": "https://...",
     *       "link": "",
     *       "title": ""
     *     }
     *   ]
     * }
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
