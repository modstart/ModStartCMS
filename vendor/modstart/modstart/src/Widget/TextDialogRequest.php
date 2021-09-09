<?php


namespace ModStart\Widget;


use ModStart\ModStart;


class TextDialogRequest extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-text-dialog-request{display:inline-block;margin-right:0.5rem;}',
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        $methods = ['muted', 'warning', 'danger', 'success', 'primary'];
        if (in_array($name, $methods)) {
            $ins = new static();
            $ins->type($name);
            $ins->text($arguments[0]);
            $ins->url($arguments[1]);
            return $ins->render();
        }
        throw new \Exception('TextDialogRequest error ' . join(',', $methods) . ' ');
    }

    public function render()
    {
        if ($this->disabled) {
            return '<a href="javascript:;" class="ub-text-dialog-request ub-text-' . $this->type . '">' . $this->text . '</a>';
        } else {
            return '<a href="javascript:;" ' . ($this->confirm ? 'data-confirm="' . $this->confirm . '"' : '')
                . ' ' . ($this->width ? 'data-dialog-width="' . $this->width . '"' : '')
                . ' ' . ($this->height ? 'data-dialog-height="' . $this->height . '"' : '')
                . ' data-dialog-request="' . $this->url . '" class="ub-text-dialog-request ub-text-' . $this->type . '">' . $this->text . '</a>';
        }
    }
}
