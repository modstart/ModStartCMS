<?php

namespace Module\Vendor\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Vendor\Event\EntryBizEvent;

/**
 * Class EntryController
 * @package Module\Vendor\Api\Controller
 * @Api 入口
 */
class EntryController extends Controller
{
    /**
     * @Api 业务入口
     * @ApiDesc 触发指定业务入口事件
     * @ApiMethod post
     * @ApiBodyParam name string required 业务名称
     * @ApiBodyParam param array 业务参数
     */
    public function biz()
    {
        $input = InputPackage::buildFromInput();
        $name = $input->getTrimString('name');
        $param = $input->getArray('param');
        EntryBizEvent::fire($name, $param);
        return Response::generateSuccess();
    }
}
