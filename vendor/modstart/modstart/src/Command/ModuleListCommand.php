<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use ModStart\Module\ModuleManager;

class ModuleListCommand extends Command
{
    protected $signature = 'modstart:module-list {type?}';

    public function handle()
    {
        $type = $this->argument('type');
        $records = [];
        switch ($type) {
            case 'system':
                $records = ModuleManager::listSystemInstalledModules();
                break;
            default:
                $records = ModuleManager::listAllInstalledModules();
                break;
        }
        foreach ($records as $name => $info) {
            echo $name . "\n";
        }
    }

}
