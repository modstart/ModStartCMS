<?php

namespace Module\AdminManager\Widget;

use App\Constant\AppConstant;
use Illuminate\Support\Str;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Widget\AbstractWidget;
use Module\AdminManager\Util\ModuleUtil;

class ServerInfoWidget extends AbstractWidget
{
    protected $view = 'module::AdminManager.View.widget.serverInfo';

    protected function variables()
    {
        $phpExtensions = get_loaded_extensions();
        $modules = SerializeUtil::jsonEncode([
            'modules' => ModuleUtil::modules(),
        ]);
        if (function_exists('gzdeflate')) {
            $modules = 'V_Z_' . base64_encode(gzdeflate($modules));
        } else {
            $modules = 'V_' . base64_encode($modules);
        }
        return [
            'modules' => $modules,
            'attributes' => $this->formatAttributes(),
            'phpExtensions' => $phpExtensions,
        ];
    }
}
