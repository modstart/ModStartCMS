<?php


namespace Module\Cms\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use Module\Cms\Util\CmsCatUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;

class MemberContentController extends Controller implements MemberLoginCheck
{
    public function delete()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $content = ModelUtil::get('cms_content', [
            'id' => $id,
            'memberUserId' => MemberUser::id(),
        ]);
        BizException::throwsIfEmpty($content, '记录不存在');
        $cat = CmsCatUtil::get($content['catId']);
        $model = $cat['_model'];
        $modelDataTable = "cms_m_" . $model['name'];
        ModelUtil::transactionBegin();;
        ModelUtil::delete('cms_content', $content['id']);
        ModelUtil::delete($modelDataTable, $content['id']);
        ModelUtil::transactionCommit();
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }
}