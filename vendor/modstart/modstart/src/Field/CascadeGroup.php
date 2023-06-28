<?php

namespace ModStart\Field;

class CascadeGroup extends AbstractField
{
    protected $isLayoutField = true;

    public static function getAssets()
    {
        return [
            // 'style' => '.ub-field-cascade-group{} .ub-field-cascade-group.cascade-group-hide{visibility:hidden;height:0;width:100%;overflow:hidden;}',
        ];
    }

    /**
     * @return string
     */
    public function render()
    {
        $column = $this->column();
        return <<<HTML
<div class="ub-field-cascade-group cascade-group-hide" id="$column">
HTML;
    }

    /**
     * @return void
     */
    public function end()
    {
        $this->context->html($this->column() . '_end')->html('</div>')->plain();
    }
}
