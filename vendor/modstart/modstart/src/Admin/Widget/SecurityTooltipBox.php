<?php

namespace ModStart\Admin\Widget;

use ModStart\Widget\AbstractWidget;

class SecurityTooltipBox extends AbstractWidget
{
    protected $view = 'modstart::admin.widget.securityTooltipBox';

    protected function variables()
    {
        return [
            'attributes' => $this->formatAttributes(),
        ];
    }
}