<?php


namespace ModStart\Widget;


use ModStart\ModStart;

/**
 * Class Label
 * @package ModStart\Widget
 *
 * @method static string primary($text, $url, $confirm = null, $disabled = false)
 * @method static string muted($text, $url, $confirm = null, $disabled = false)
 * @method static string warning($text, $url, $confirm = null, $disabled = false)
 * @method static string danger($text, $url, $confirm = null, $disabled = false)
 * @method static string success($text, $url, $confirm = null, $disabled = false)
 *
 * @method $this text($text)
 * @method $this type($type)
 * @method $this confirm($text)
 * @method $this url($url)
 * @method $this disabled($boolean)
 * @method $this attr($attr)
 */
class TextAjaxRequest extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-text-ajax-request{display:inline-block;margin-right:0.5rem;}',
        ];
    }

    /**
     * @param ...$arguments string type,text,url
     * @return $this
     */
    public static function make(...$arguments)
    {
        $ins = new static();
        $ins->type($arguments[0]);
        $ins->text($arguments[1]);
        $ins->url($arguments[2]);
        return $ins;
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
            return '<a href="javascript:;" ' . ($this->confirm ? 'data-confirm="' . $this->confirm . '"' : '')
                . ' data-ajax-request-loading data-ajax-request="' . $this->url
                . '" class="ub-text-ajax-request ub-text-' . $this->type . '" '
                . ' ' . ($this->attr ? $this->attr : '')
                . '>'
                . $this->text . '</a>';
        }
    }
}
