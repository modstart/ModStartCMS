<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use ModStart\Core\Events\ModuleUninstalledEvent;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\VersionUtil;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleUninstallCommand extends Command
{
    protected $signature = 'modstart:module-uninstall {module}';

    public function handle()
    {
        $module = $this->argument('module');
        $isExists = ModuleManager::isExists($module);
        $installeds = ModuleManager::listAllInstalledModules();
        if ($isExists) {
            BizException::throwsIf(L('Module not installed'), !isset($installeds[$module]));
            foreach ($installeds as $one => $_) {
                $basic = ModuleManager::getModuleBasic($one);
                if (empty($basic)) {
                    break;
                }
                if (!empty($basic['require'])) {
                    foreach ($basic['require'] as $require) {
                        list($m, $v) = VersionUtil::parse($require);
                        BizException::throwsIf(L('Module %s depend on %s, uninstall fail', $one, $module), $module == $m);
                    }
                }
            }
            ModuleManager::callHook($module, 'hookBeforeUninstall');
        }
        unset($installeds[$module]);
        $this->unPublishAsset($module);
        $this->unPublishRoot($module);

        ModuleManager::saveUserInstalledModules($installeds);

        ModStart::clearCache();

        $event = new ModuleUninstalledEvent();
        $event->name = $module;
        if (PHP_VERSION_ID >= 80000) {
            Event::dispatch($event);
        } else {
            Event::fire($event);
        }
    }

    private function unPublishAsset($module)
    {
        $fs = $this->laravel['files'];
        $from = ModuleManager::path($module, 'Asset') . '/';
        if (!file_exists($from)) {
            return;
        }
        $to = public_path("vendor/$module/");
        if (!file_exists($to)) {
            return;
        }
        $fs->deleteDirectory($to);
        $this->info("Module Asset UnPublish : $to");
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
            if (!file_exists($currentFile)) {
                continue;
            }
            if (
                (!file_exists($currentFileBackup) && md5_file($currentFile) == md5_file($file['pathname']))
                ||
                (file_exists($currentFileBackup))
            ) {
                unlink($currentFile);
                if (file_exists($currentFileBackup)) {
                    $content = trim(file_get_contents($currentFileBackup));
                    if ('__MODSTART_EMPTY_FILE__' == $content) {
                        unlink($currentFileBackup);
                        $this->info("Module Root Publish : $relativePath");
                    } else {
                        rename($currentFileBackup, $currentFile);
                        $this->info("Module Root Publish : $relativePath <- $relativePathBackup");
                    }
                }
                $publishFiles++;
            }
        }
        $this->info("Module Root UnPublish : $publishFiles item(s)");
    }

}
