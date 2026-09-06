<?php

namespace Module\Member\Api\Controller;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Member\Model\MemberUser;

/**
 * @Api 会员管理
 */
class AdminMemberController extends Controller
{
    /**
     * @Api 根据昵称搜索会员
     * @ApiDesc 根据关键词模糊匹配会员昵称，返回匹配的会员列表
     * @ApiMethod post
     * @ApiBodyParam ADMIN_API_KEY string required 对接Key
     * @ApiBodyParam keyword string required 搜索关键词
     * @ApiResponseData {
     *   "list": [
     *     {
     *       "id": 1,
     *       "username": "admin",
     *       "nickname": "管理员"
     *     }
     *   ]
     * }
     */
    public function searchByNickname()
    {
        $input = InputPackage::buildFromInput();
        $keyword = $input->getTrimString('keyword');
        BizException::throwsIfEmpty('关键词不能为空', $keyword);

        $list = ModelUtil::model(MemberUser::class)
            ->where('nickname', 'like', '%' . $keyword . '%')
            ->where(function ($q) {
                $q->where('isDeleted', 0)->orWhereNull('isDeleted');
            })
            ->select(['id', 'username', 'nickname', 'avatar'])
            ->limit(20)
            ->get()
            ->toArray();

        return Response::generateSuccessData([
            'list' => $list,
        ]);
    }

    /**
     * @Api 根据字段随机获取用户ID
     * @ApiDesc 根据指定字段（bizGroup/username/nickname）和值匹配用户，从匹配结果中随机返回一个用户ID，未匹配时返回 id=0
     * @ApiMethod post
     * @ApiBodyParam ADMIN_API_KEY string required 对接Key
     * @ApiBodyParam field string required 查询字段，支持 bizGroup、username、nickname
     * @ApiBodyParam value string required 查询值
     * @ApiResponseData {
     *   "id": 1
     * }
     */
    public function randomBy()
    {
        $input = InputPackage::buildFromInput();
        $field = $input->getTrimString('field');
        $value = $input->getTrimString('value');

        BizException::throwsIfEmpty('field 不能为空', $field);
        BizException::throwsIfEmpty('value 不能为空', $value);

        $allowedFields = ['bizGroup', 'username', 'nickname'];
        BizException::throwsIf('field 无效，仅支持 bizGroup、username、nickname', !in_array($field, $allowedFields));

        if (!Schema::hasColumn('member_user', $field)) {
            return Response::generateSuccessData([
                'id' => 0,
            ]);
        }

        $list = ModelUtil::model(MemberUser::class)
            ->where($field, $value)
            ->where(function ($q) {
                $q->where('isDeleted', 0)->orWhereNull('isDeleted');
            })
            ->select(['id'])
            ->get()
            ->toArray();

        if (empty($list)) {
            return Response::generateSuccessData([
                'id' => 0,
            ]);
        }

        $random = $list[array_rand($list)];

        return Response::generateSuccessData([
            'id' => $random['id'],
        ]);
    }
}