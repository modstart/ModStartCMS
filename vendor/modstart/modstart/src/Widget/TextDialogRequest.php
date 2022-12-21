<?php


namespace ModStart\Widget;


use ModStart\ModStart;

/**
 * Class Label
 * @package ModStart\Widget
 *
 * @method static string primary($text, $url, $disabled = false)
 * @method static string muted($text, $url, $disabled = false)
 * @method static string warning($text, $url, $disabled = false)
 * @method static string danger($text, $url, $disabled = false)
 * @method static string success($text, $url, $disabled = false)
 *
 * @method $this text($text)
 * @method $this type($type)
 * @method $this url($url)
 * @method $this disabled($boolean)
 * @method $this width($value)
 * @method $this height($value)
 * @method $this attr($attr)
 */
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
            if (isset($arguments[2])) {
                $ins->disabled($arguments[2]);
            }
            return $ins->render();
        }
        throw new \Exception('TextDialogRequest error ' . join(',', $methods) . ' ');
    }

    /**
     * @param $type string
     * @param $text string
     * @param $text url
     * @return TextDialogRequest
     */
    public static function make(...$arguments)
    {
        $ins = new static();
        $ins->type($arguments[0]);
        $ins->text($arguments[1]);
        $ins->url($arguments[2]);
        return $ins;
    }

    /**
     * @param $size string big|default
     * @return $this
     */
    public function size($size)
    {
        switch ($size) {
            case 'big':
                return $this->attr(($this->attr ? $this->attr : '') . ' data-dialog-width="90%" data-dialog-height="90%"');
        }
        return $this;
    }

    public function render()
    {
        $type = $this->type;
        if ('primary' == $type) {
            $type = 'link';
        }
        if ($this->disabled) {
            return '<a href="javascript:;" class="ub-text-dialog-request ub-text-' . $type . '">' . $this->text . '</a>';
        } else {
            return '<a href="javascript:;" ' . ($this->confirm ? 'data-confirm="' . $this->confirm . '"' : '')
                . ' ' . ($this->width ? 'data-dialog-width="' . $this->width . '"' : '')
                . ' ' . ($this->height ? 'data-dialog-height="' . $this->height . '"' : '')
                . ' ' . ($this->attr ? $this->attr : '')
                . ' data-dialog-request="' . $this->url . '" class="ub-text-dialog-request ub-text-' . $type . '">' . $this->text . '</a>';
        }
    }
}
