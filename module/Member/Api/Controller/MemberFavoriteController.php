<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberFavoriteUtil;

/**
 * Class MemberFavoriteController
 * @package Module\Member\Api\Controller
 * @Api 用户操作
 */
class MemberFavoriteController extends Controller implements MemberLoginCheck
{
    /**
     * @Api 收藏提交
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 收藏指定内容
     * @ApiBodyParam category string required 类别
     * @ApiBodyParam categoryId int required 类别ID
     * @ApiBodyParam redirect string 收藏成功跳转地址
     * @ApiResponseData {
     *   "code": 0
     * }
     */
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

    /**
     * @Api 收藏取消
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 取消收藏指定内容
     * @ApiBodyParam category string required 类别
     * @ApiBodyParam categoryId int required 类别ID
     * @ApiBodyParam redirect string 取消收藏跳转地址
     * @ApiResponseData {
     *   "code": 0
     * }
     */
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
