<?php


namespace ModStart\Widget;


use ModStart\ModStart;


class TextAjaxRequest extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-text-ajax-request{display:inline-block;margin-right:0.5rem;}',
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        $methods = ['primary', 'muted', 'warning', 'danger', 'success',];
        if (in_array($name, $methods)) {
            $ins = new static();
            $ins->type($name);
            $ins->text($arguments[0]);
            $ins->url($arguments[1]);
            $ins->confirm(empty($arguments[2]) ? '' : $arguments[2]);
            $ins->disabled(empty($arguments[3]) ? false : true);
            return $ins->render();
        }
        throw new \Exception('TextAjaxRequest error ' . join(',', $methods) . ' ');
    }

    public function render()
    {
        if ($this->disabled) {
            return '<a href="javascript:;" class="ub-text-ajax-request ub-text-' . $this->type . '">' . $this->text . '</a>';
        } else {
            return '<a href="javascript:;" ' . ($this->confirm ? 'data-confirm="' . $this->confirm . '"' : '') . ' data-ajax-request-loading data-ajax-request="' . $this->url . '" class="ub-text-ajax-request ub-text-' . $this->type . '">' . $this->text . '</a>';
        }
    }
}