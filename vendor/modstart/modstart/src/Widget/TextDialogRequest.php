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
 * @method TextDialogRequest text($text)
 * @method TextDialogRequest type($type)
 * @method TextDialogRequest url($url)
 * @method TextDialogRequest disabled($boolean)
 * @method TextDialogRequest width($value)
 * @method TextDialogRequest height($value)
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
     * @param mixed ...$arguments
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
                . ' data-dialog-request="' . $this->url . '" class="ub-text-dialog-request ub-text-' . $type . '">' . $this->text . '</a>';
        }
    }
}
