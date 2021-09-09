<?php


namespace ModStart\Widget;


use ModStart\ModStart;


class TextLink extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-text-link{display:inline-block;margin-right:0.5rem;}',
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        $methods = ['primary', 'muted', 'warning', 'danger', 'success',];
        if (in_array($name, $methods)) {
            $ins = new static();
            $ins->type($name);
            $ins->text($arguments[0]);
            $ins->link($arguments[1]);
            $ins->attr(empty($arguments[2]) ? '' : $arguments[2]);
            return $ins->render();
        }
        throw new \Exception('TextLink error ' . join(',', $methods) . ' ');
    }

    public function render()
    {
        if ($this->disabled) {
            return '<a href="javascript:;" class="ub-text-link ub-text-muted">' . $this->text . '</a>';
        } else {
            return '<a href="' . $this->link . '" class="ub-text-link ub-text-' . $this->type . '" ' . $this->attr . '>' . $this->text . '</a>';
        }
    }
}
