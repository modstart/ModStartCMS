<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use ModStart\Core\Events\ModuleEnabledEvent;
use ModStart\Core\Exception\BizException;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleEnableCommand extends Command
{
    protected $signature = 'modstart:module-enable {module}';

    public function handle()
    {
        $module = $this->argument('module');
        BizException::throwsIf(L('Module Invalid'), !ModuleManager::isExists($module));
        $installeds = ModuleManager::listAllInstalledModules();
        $basic = ModuleManager::getModuleBasic($module);
        BizException::throwsIf('Module basic empty', !$basic);
        $installeds[$module]['enable'] = true;
        ModuleManager::saveUserInstalledModules($installeds);
        ModStart::clearCache();

        if (method_exists(ModuleManager::class, 'callHook')) {
            ModuleManager::callHook($module, 'hookEnabled');
        }

        $event = new ModuleEnabledEvent();
        $event->name = $module;
        Event::fire($event);
        $this->info('Module Enable Success');
    }

}
