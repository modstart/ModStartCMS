<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Type\CmsContentVerifyStatus;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsTemplateUtil;
use Module\Member\Auth\MemberUser;

class DetailController extends ModuleBaseController
{
    public function index($id = 0)
    {
        if (is_numeric($id)) {
            $data = CmsContentUtil::get($id);
        } else {
            $data = CmsContentUtil::getByAlias($id);
        }
        $cat = CmsCatUtil::get($data['record']['catId']);
        $catChain = CmsCatUtil::chain($cat['id']);
        $catRoot = CmsCatUtil::root($cat['id']);
        $catRootChildren = CmsCatUtil::children($catRoot['id']);
        $view = $cat['detailTemplate'];
        if (empty($view)) {
            $view = $data['model']['detailTemplate'];
        }
        BizException::throwsIfEmpty('模板未找到', $view);
        $viewData = [];
        $viewData['record'] = $data['record'];
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

        // return $viewData;
        return $this->view('cms.detail.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }
}
