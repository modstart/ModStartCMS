<?php

namespace Module\AdminManager\Widget;

use ModStart\ModStart;
use ModStart\Module\ModuleManager;
use ModStart\Widget\AbstractWidget;

class ServerInfoWidget extends AbstractWidget
{
    protected $view = 'module::AdminManager.View.widget.serverInfo';

    protected function variables()
    {
        $modules = [];
        $modules[] = "ModStart:" . ModStart::$version;
        foreach (ModuleManager::listAllEnabledModules() as $m => $_) {
            $info = ModuleManager::getModuleBasic($m);
            $modules[] = "$m:$info[version]";
        }
        return [
            'modules' => $modules,
            'attributes' => $this->formatAttributes(),
        ];
    }
}
