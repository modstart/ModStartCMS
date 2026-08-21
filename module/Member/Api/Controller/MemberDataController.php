<?php


namespace Module\Member\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Data\DataStorageType;
use ModStart\Data\FileManager;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;

/**
 * Class MemberDataController
 * @package Module\Member\Api\Controller
 * @Api 用户信息
 */
class MemberDataController extends Controller implements MemberLoginCheck
{
    /**
     * @Api 用户文件管理
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 用户文件上传与管理
     * @ApiQueryParam category string required 类别
     * @ApiBodyParam action string required 动作，uploadDirect表示文件上传
     * @ApiBodyParam file File 文件对象
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "path": "上传文件路径"
     *   }
     * }
     */
    public function fileManager($category, $storageTypeConfig = null)
    {
        $option = [];
        if ($storageTypeConfig) {
            $storageType = modstart_config($storageTypeConfig, '');
            if ($storageType) {
                $option['driver'] = DataStorageType::toDriverName($storageType);
            }
        }
        return FileManager::handle(
            $category,
            'member_upload',
            'member_upload_category',
            MemberUser::id(),
            $option,
            null,
            [
                'eventOpt' => [
                    'param' => [
                        'userType' => 'member',
                        'userId' => MemberUser::id(),
                    ]
                ]
            ]
        );
    }
}
