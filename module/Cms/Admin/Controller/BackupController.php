<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use ModStart\Module\ModuleManager;

class BackupController extends Controller
{
    public function index()
    {
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            $input = InputPackage::buildFromInput();
            $filename = $input->getTrimString('filename');
            BizException::throwsIfEmpty('备份名称为空', $filename);
            BizException::throwsIf('备份名称不合规', !preg_match('/^[a-zA-Z0-9_]+$/', $filename));
            $module = $input->getTrimString('module');
            $tables = $input->getArray('table');
            BizException::throwsIfEmpty('备份保存目录为空', $module);
            BizException::throwsIf('模块不存在', !ModuleManager::isExists($module));
            BizException::throwsIfEmpty('备份保存目录为空', $tables);
            $savePath = ModuleManager::path($module, 'Backup/' . $filename . '.json');
            BizException::throwsIf('备份文件已经存在', file_exists($savePath));
            $backup = [
                'structure' => [],
                'backup' => [],
            ];
            foreach ($tables as $table) {
                $backup['structure'][$table] = ModelManageUtil::tableStructure($table);
                $backup['backup'][$table] = ModelUtil::all($table);
            }
            FileUtil::write($savePath, json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return Response::generateSuccess('备份成功');
        }
        return view('module::Cms.View.admin.backup.index');
    }
}