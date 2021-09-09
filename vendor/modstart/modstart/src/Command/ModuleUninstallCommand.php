<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleUninstallCommand extends Command
{
    protected $signature = 'modstart:module-uninstall {module}';

    public function handle()
    {
        $module = $this->argument('module');
        BizException::throwsIf(L('Module Invalid'), !ModuleManager::isExists($module));
        $installeds = ModuleManager::listAllInstalledModules();
        BizException::throwsIf(L('Module not installed'), !isset($installeds[$module]));
        foreach ($installeds as $one => $_) {
            $basic = ModuleManager::getModuleBasic($one);
            BizException::throwsIf('Module[' . $one . '] config empty', !$basic);
            if (in_array($module, $basic['require'])) {
                return Response::generateError(L('Module %s depend on %s, uninstall fail', $one, $module));
            }
        }
        unset($installeds[$module]);

        $this->unPublishRoot($module);

        ModuleManager::saveUserInstalledModules($installeds);

        ModStart::clearCache();
    }

    private function unPublishRoot($module)
    {
        $root = ModuleManager::path($module, 'ROOT');
        if (!file_exists($root)) {
            return;
        }
        $files = FileUtil::listAllFiles($root);
        $files = array_filter($files, function ($file) {
            return $file['isFile'];
        });
        $publishFiles = 0;
        foreach ($files as $file) {
            $relativePath = $file['filename'];
            $relativePathBackup = $relativePath . '._delete_.' . $module;
            $currentFile = base_path($relativePath);
            $currentFileBackup = $currentFile . '._delete_.' . $module;
            if (
                (!file_exists($currentFileBackup) && md5_file($currentFile) == md5_file($file['pathname']))
                ||
                (file_exists($currentFileBackup))
            ) {
                unlink($currentFile);
                if (file_exists($currentFileBackup)) {
                    rename($currentFileBackup, $currentFile);
                }
                $publishFiles++;
                $this->info("Module Root Publish : $relativePath <- $relativePathBackup");
            }
        }
        $this->info("Module Root UnPublish : $publishFiles item(s)");
    }

}
