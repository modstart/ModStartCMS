<?php


namespace Module\Member\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\Admin;
use ModStart\Data\FileManager;
use ModStart\Data\UeditorManager;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;

/**
 * Class MemberDataController
 * @package Module\Member\Api\Controller
 * @Api 用户文件
 */
class MemberDataController extends Controller implements MemberLoginCheck
{
    /**
     * @param $category
     * @return mixed
     * @Api 用户文件管理
     * @ApiQueryParam category string 类别
     * @ApiBodyParam action string 动作，uploadDirect表示文件上传
     * @ApiBodyParam file File 文件对象
     */
    public function fileManager($category)
    {
        return FileManager::handle(
            $category,
            'member_upload',
            'member_upload_category',
            MemberUser::id()
        );
    }
}
