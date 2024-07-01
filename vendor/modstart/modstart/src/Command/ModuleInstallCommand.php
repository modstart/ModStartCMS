<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use ModStart\Core\Events\ModuleInstalledEvent;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\VersionUtil;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleInstallCommand extends Command
{
    protected $signature = 'modstart:module-install {module} {--link-asset} {--force}';

    public function handle()
    {
        $module = $this->argument('module');
        BizException::throwsIf(L('Module Invalid'), !ModuleManager::isExists($module));
        $installeds = ModuleManager::listAllInstalledModules();
        $basic = ModuleManager::getModuleBasic($module);
        BizException::throwsIf('Module basic empty', !$basic);
        BizException::throwsIf(L('Module %s:%s depends on ModStart:%s, install fail', $module, $basic['version'], $basic['modstartVersion']), !VersionUtil::match(ModStart::$version, $basic['modstartVersion']));
        $env = ModStart::env();
        BizException::throwsIf(
            L('Module %s:%s compatible with env %s, current is %s', $module, $basic['version'], join(',', $basic['env']), $env),
            !in_array($env, $basic['env'])
        );
        foreach ($basic['require'] as $require) {
            list($m, $v) = VersionUtil::parse($require);
            BizException::throwsIf(L('Module %s:%s depend on %s:%s, install fail', $module, $basic['version'], $m, $v), !isset($installeds[$m]));
            $mBasic = ModuleManager::getModuleBasic($m);
            BizException::throwsIf(L('Module %s:%s depend on %s:%s, install fail', $module, $basic['version'], $m, $v), !VersionUtil::match($mBasic['version'], $v));
        }
        if (!empty($basic['conflicts'])) {
            foreach ($basic['conflicts'] as $conflict) {
                list($m, $v) = VersionUtil::parse($conflict);
                if (!isset($installeds[$m])) {
                    continue;
                }
                $mBasic = ModuleManager::getModuleBasic($m);
                BizException::throwsIf(L('Module %s:%s conflict with %s:%s, install fail', $module, $basic['version'], $m, $v), VersionUtil::match($mBasic['version'], $v));
            }
        }
        $output = null;

        $this->migrate($module);
        $this->publishAsset($module);
        $this->publishRoot($module);

        if (!isset($installeds[$module])) {
            $installeds[$module] = [
                'isSystem' => ModuleManager::isSystemModule($module),
                'enable' => false,
                'config' => []
            ];
            ModuleManager::saveUserInstalledModules($installeds);
        }

        ModStart::clearCache();

        ModuleManager::callHook($module, 'hookInstalled');

        $event = new ModuleInstalledEvent();
        $event->name = $module;
        if (PHP_VERSION_ID >= 80000) {
            Event::dispatch($event);
        } else {
            Event::fire($event);
        }

        $this->info('Module Install Success');
    }

    private function migrate($module)
    {
        $path = ModuleManager::path($module, 'Migrate');
        if (!file_exists($path)) {
            return;
        }
        $this->info('Module Migrate Success');
        $this->call('migrate', ['--path' => ModuleManager::relativePath($module, 'Migrate'), '--force' => true]);
    }

    private function publishRoot($module)
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
            if (file_exists($currentFile) && !file_exists($currentFileBackup)) {
                rename($currentFile, $currentFileBackup);
                $this->info("Module Root Publish : $relativePath -> $relativePathBackup");
            }
            if (!file_exists($currentFile) || md5_file($currentFile) != file_get_contents($file['pathname'])) {
                FileUtil::ensureFilepathDir($currentFile);
                file_put_contents($currentFile, file_get_contents($file['pathname']));
                if (!file_exists($currentFileBackup)) {
                    file_put_contents($currentFileBackup, '__MODSTART_EMPTY_FILE__');
                }
                $publishFiles++;
            }
        }
        $this->info("Module Root Publish : $publishFiles item(s)");
    }

    private function publishAsset($module)
    {
        $force = $this->option('force');
        $linkAsset = $this->option('link-asset');
        $fs = $this->laravel['files'];
        $from = ModuleManager::path($module, 'Asset') . '/';
        if (!file_exists($from)) {
            return;
        }
        $to = public_path("vendor/$module/");
        if (file_exists($to) && !$force) {
            $this->info("Module Asset Publish : Ignore");
            return;
        }
        if (!file_exists(public_path('vendor'))) {
            @mkdir(public_path('vendor'), 0755);
        }
        if ($linkAsset) {
            $linkFromRelative = ModuleManager::relativePath($module, 'Asset');
            $linkFrom = ModuleManager::path($module, 'Asset');
            $linkToRelative = "vendor/$module";
            $linkTo = public_path($linkToRelative);
            FileUtil::link($linkFrom, $linkTo);
        } else {
            $fs->deleteDirectory($to);
            $fs->copyDirectory($from, $to);
            $this->info("Module Asset Publish : $from -> $to");
        }
    }

}
