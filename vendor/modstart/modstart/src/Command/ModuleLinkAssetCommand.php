<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;
use ModStart\Module\ModuleManager;

class ModuleLinkAssetCommand extends Command
{
    protected $signature = 'modstart:module-link-asset {module}';

    public function handle()
    {
        $module = $this->argument('module');

        BizException::throwsIf('模块不存在', !ModuleManager::isExists($module));
        $linkFromRelative = ModuleManager::relativePath($module, 'Asset');
        $linkFrom = ModuleManager::path($module, 'Asset');
        $linkToRelative = "vendor/$module";
        $linkTo = public_path($linkToRelative);

        if (file_exists($linkTo)) {
            $this->error("The [$linkToRelative] link already exists.");
            return;
        }

        FileUtil::link($linkFrom, $linkTo);
        $this->info("The [$linkToRelative] link has been connected to [$linkFromRelative]");
    }

}
