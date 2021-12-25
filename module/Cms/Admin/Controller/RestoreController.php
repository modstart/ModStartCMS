<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleManager;
use Module\Cms\Util\CmsBackupUtil;

class RestoreController extends Controller
{
    public function index()
    {
        return view('module::Cms.View.admin.restore.index', [
            'backups' => CmsBackupUtil::listBackups(),
        ]);
    }

    public function delete()
    {
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $module = $input->getTrimString('module');
        $filename = $input->getTrimString('filename');
        BizException::throwsIfEmpty('名称为空', $filename);
        BizException::throwsIf('名称不合规', !preg_match('/^[a-zA-Z0-9_]+\\.json$/', $filename));
        BizException::throwsIfEmpty('备份保存目录为空', $module);
        BizException::throwsIf('模块不存在', !ModuleManager::isExists($module));
        $savePath = ModuleManager::path($module, 'Backup/' . $filename);
        BizException::throwsIf('备份文件不存在', !file_exists($savePath));
        @unlink($savePath);
        return Response::generate(0, '删除成功', null, '[reload]');
    }

    public function submit()
    {
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $module = $input->getTrimString('module');
        $filename = $input->getTrimString('filename');
        BizException::throwsIfEmpty('名称为空', $filename);
        BizException::throwsIf('名称不合规', !preg_match('/^[a-zA-Z0-9_]+\\.json$/', $filename));
        BizException::throwsIfEmpty('备份保存目录为空', $module);
        BizException::throwsIf('模块不存在', !ModuleManager::isExists($module));
        $savePath = ModuleManager::path($module, 'Backup/' . $filename);
        BizException::throwsIf('备份文件不存在', !file_exists($savePath));
        $data = @json_decode(file_get_contents($savePath), true);
        CmsBackupUtil::restoreBackup($data);
        return Response::generateSuccess('恢复成功');
    }
}