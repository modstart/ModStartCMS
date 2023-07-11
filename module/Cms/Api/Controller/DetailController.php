<?php


namespace Module\Cms\Api\Controller;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Type\CmsContentVerifyStatus;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsMemberPermitUtil;
use Module\Member\Auth\MemberUser;

/**
 * @Api 通用CMS
 */
class DetailController extends ModuleBaseController
{
    /**
     * @Api 内容-获取详情
     * @ApiBodyParam id integer 内容ID
     */
    public function index()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getTrimString('id');
        if (is_numeric($id)) {
            $data = CmsContentUtil::get($id);
        } else {
            $data = CmsContentUtil::getByAlias($id);
        }
        CmsContentUtil::increaseView($data['record']['id']);
        $cat = CmsCatUtil::get($data['record']['catId']);
        BizException::throwsIfEmpty('栏目不存在', $cat);
        $record = $data['record'];
        $view = $record['detailTemplate'];
        if (empty($view)) {
            $view = $cat['detailTemplate'];
        }
        if (empty($view)) {
            $view = $data['model']['detailTemplate'];
        }
        $catChain = CmsCatUtil::chain($cat['id']);
        $catRoot = CmsCatUtil::root($cat['id']);
        $catRootChildren = CmsCatUtil::children($catRoot['id']);
        $viewData = [];
        $viewData['view'] = $view;
        if (!CmsMemberPermitUtil::canVisitCat($cat)) {
            $record = ArrayUtil::keepKeys($record, [
                'title', 'summary', 'cover', 'catId', 'id',
                'seoTitle', 'seoKeywords', 'seoDescription',
            ]);
        }
        $viewData['record'] = $record;
        $viewData['recordPrev'] = \MCms::prevContent($record['catId'], $record['id']);
        $viewData['recordNext'] = \MCms::nextContent($record['catId'], $record['id']);
        $viewData['cat'] = $cat;
        $viewData['catRoot'] = $catRoot;
        $viewData['catChain'] = $catChain;
        $viewData['catRootChildren'] = $catRootChildren;
        $viewData['model'] = $data['model'];
        if ($data['record']['verifyStatus'] != CmsContentVerifyStatus::VERIFY_PASS) {
            if (MemberUser::isNotMine($data['record']['memberUserId'])) {
                BizException::throws('记录未审核');
            }
        }
        return Response::generateSuccessData($viewData);
    }
}
