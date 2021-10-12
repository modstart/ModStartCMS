<?php


namespace ModStart\Widget;


use ModStart\ModStart;

/**
 * Class Label
 * @package ModStart\Widget
 *
 * @method static string muted($text, $url, $confirm = null, $disabled = false)
 * @method static string warning($text, $url, $confirm = null, $disabled = false)
 * @method static string danger($text, $url, $confirm = null, $disabled = false)
 * @method static string success($text, $url, $confirm = null, $disabled = false)
 *
 * @method void text($text)
 * @method void type($type)
 * @method void confirm($text)
 * @method void url($url)
 * @method void disabled($boolean)
 */
class ButtonAjaxRequest extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-button-ajax-request{display:inline-block;}',
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
            $ins->confirm(empty($arguments[2]) ? '' : $arguments[2]);
            $ins->disabled(empty($arguments[3]) ? false : true);
            return $ins->render();
        }
        throw new \Exception('ButtonAjaxRequest error ' . join(',', $methods) . ' ');
    }

    public function render()
    {
        if ($this->disabled) {
            return '<a href="javascript:;" class="btn ub-button-ajax-request btn-' . $this->type . '">' . $this->text . '</a>';
        } else {
            return '<a href="javascript:;" ' . ($this->confirm ? 'data-confirm="' . $this->confirm . '"' : '') . ' data-ajax-request-loading data-ajax-request="' . $this->url . '" class="btn ub-button-ajax-request btn-' . $this->type . '">' . $this->text . '</a>';
        }
    }
}