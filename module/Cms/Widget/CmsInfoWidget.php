<?php

namespace Module\Cms\Widget;

use ModStart\Widget\AbstractWidget;

class CmsInfoWidget extends AbstractWidget
{
    protected $view = 'module::Cms.View.widget.cmsInfo';

    protected function variables()
    {
        return [
            'attributes' => $this->formatAttributes(),
        ];
    }
}
