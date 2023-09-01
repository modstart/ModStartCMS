<?php


namespace Module\Cms\Api\Controller;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Util\CmsOperateUtil;

/**
 * @Api 通用CMS
 */
class OperateController extends ModuleBaseController
{
    /**
     * @Api 操作-匿名点赞/取消点赞
     * @ApiBodyParam id integer 内容ID
     */
    public function like()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        BizException::throwsIfEmpty('内容ID为空', $id);
        $ret = CmsOperateUtil::like($id);
        BizException::throwsIfResponseError($ret);
        return Response::generateSuccessData([
            'action' => $ret['data']['action'],
            'update' => [
                'like-count' => $ret['data']['count']
            ]
        ]);
    }
}
