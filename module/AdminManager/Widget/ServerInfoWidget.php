<?php

namespace Module\AdminManager\Widget;

use ModStart\Widget\AbstractWidget;
use Module\AdminManager\Util\ModuleUtil;

class ServerInfoWidget extends AbstractWidget
{
    protected $view = 'module::AdminManager.View.widget.serverInfo';

    protected function variables()
    {
        $phpExtensions = get_loaded_extensions();
        return [
            'modules' => ModuleUtil::modules(),
            'attributes' => $this->formatAttributes(),
            'phpExtensions' => $phpExtensions,
        ];
    }
}
