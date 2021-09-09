<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberFavoriteUtil;

class MemberFavoriteController extends Controller implements MemberLoginCheck
{
    public function favorite()
    {
        $input = InputPackage::buildFromInput();
        $category = $input->getTrimString('category');
        $categoryId = $input->getTrimString('categoryId');
        BizException::throwsIfEmpty('category为空', $category);
        BizException::throwsIfEmpty('categoryId为空', $categoryId);
        if (MemberFavoriteUtil::exists(MemberUser::id(), $category, $categoryId)) {
            return Response::generateError('已经收藏');
        }
        MemberFavoriteUtil::add(MemberUser::id(), $category, $categoryId);
        if ($redirect = $input->getTrimString('redirect')) {
            return Response::generate(0, null, null, $redirect);
        }
        return Response::generateSuccess();
    }

    public function unfavorite()
    {
        $input = InputPackage::buildFromInput();
        $category = $input->getTrimString('category');
        $categoryId = $input->getTrimString('categoryId');
        BizException::throwsIfEmpty('category为空', $category);
        BizException::throwsIfEmpty('categoryId为空', $categoryId);
        if (!MemberFavoriteUtil::exists(MemberUser::id(), $category, $categoryId)) {
            return Response::generateError('未收藏');
        }
        MemberFavoriteUtil::delete(MemberUser::id(), $category, $categoryId);
        if ($redirect = $input->getTrimString('redirect')) {
            return Response::generate(0, null, null, $redirect);
        }
        return Response::generateSuccess();
    }

}
