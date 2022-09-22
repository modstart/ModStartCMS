<?php

namespace Module\AdminManager\Admin\Controller;

use App\Constant\AppConstant;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use Module\AdminManager\Util\ModuleUtil;
use Module\AdminManager\Util\UpgradeUtil;

class UpgradeController extends Controller
{
    public static $PermitMethodMap = [
        'index' => '@SystemUpgrade',
        'info' => '@SystemUpgrade',
        'auth' => '@SystemUpgrade',
    ];

    private function doFinish($msgs, $logs = null)
    {
        return Response::generateSuccessData([
            'msg' => array_map(function ($item) {
                return '<i class="iconfont icon-hr"></i> ' . $item;
            }, $msgs),
            'logs' => $logs,
            'finish' => true,
        ]);
    }

    private function doNext($step, $msgs = [], $data = [])
    {
        $input = InputPackage::buildFromInput();
        $data = array_merge($input->getJsonAsInput('data')->all(), $data);
        return Response::generateSuccessData([
            'msg' => array_map(function ($item) {
                if (!starts_with($item, '<')) {
                    $item = "<span class='ub-text-white'>$item</span>";
                }
                return '<i class="iconfont icon-hr"></i> ' . $item;
            }, $msgs),
            'step' => $step,
            'data' => $data,
            'finish' => false,
        ]);
    }

    public function index()
    {
        if (config('modstart.admin.upgradeDisable', false)) {
            return Response::sendError('系统升级功能已关闭');
        }
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            $input = InputPackage::buildFromInput();
            $step = $input->getTrimString('step');
            $token = $input->getTrimString('token');
            $dataInput = $input->getJsonAsInput('data');
            $toVersion = $dataInput->getTrimString('toVersion');
            BizException::throwsIfEmpty('toVersion为空', $toVersion);
            switch ($step) {
                case 'upgradePackage':
                    $package = $dataInput->getTrimString('package');
                    $diffContentFile = $dataInput->getTrimString('diffContentFile');
                    BizException::throwsIfEmpty('package为空', $package);
                    BizException::throwsIfEmpty('diffContentFile为空', $diffContentFile);
                    $ret = UpgradeUtil::upgradePackage($package, $diffContentFile);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">升级完成，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ], $ret['data']['logs']);
                case 'downloadPackage':
                    $ret = UpgradeUtil::downloadPackage($token, AppConstant::APP, AppConstant::VERSION, $toVersion);
                    BizException::throwsIfResponseError($ret);
                    return $this->doNext('upgradePackage', [
                        '<span class="ub-text-success">获取安装包完成，大小 ' . FileUtil::formatByte($ret['data']['packageSize']) . '</span>',
                        '<span class="ub-text-white">开始解压升级装包...</span>'
                    ], [
                        'package' => $ret['data']['package'],
                        'diffContentFile' => $ret['data']['diffContentFile'],
                    ]);
                case 'checkPackage':
                    try {
                        $exitCode = Artisan::call("migrate");
                    } catch (\Exception $e) {
                        $exitCode = -1;
                    }
                    BizException::throwsIf("调用 php artisan 命令失败，不能自动升级", 0 != $exitCode);
                    return $this->doNext('downloadPackage', [
                        'PHP版本: v' . PHP_VERSION,
                        '<span class="ub-text-success">预检通过</span>',
                        '<span class="ub-text-white">开始下载升级包...</span>'
                    ]);
                default:
                    return $this->doNext('checkPackage', [
                        '<span class="ub-text-success">开始升级系统，从 V' . AppConstant::VERSION . ' 到 V' . $toVersion . '</span>',
                        '<span class="ub-text-white">开始预检系统...</span>'
                    ]);
            }
        }
        return view('module::AdminManager.View.admin.upgrade');
    }

    public function auth()
    {
        return view('module::AdminManager.View.admin.auth', [
            'modules' => ModuleUtil::modules(),
        ]);
    }

    public function info()
    {
        $latest = UpgradeUtil::latest();
        return Response::generateSuccessData([
            'version' => AppConstant::VERSION,
            'latestVersion' => $latest['latestVersion'],
            'autoUpgrade' => $latest['autoUpgrade'],
        ]);
    }

}
