<?php


namespace ModStart\Widget;


/**
 * Class Label
 * @package ModStart\Widget
 *
 * @method static ButtonLink muted($text, $url, $disabled = false)
 * @method static ButtonLink primary($text, $url, $disabled = false)
 * @method static ButtonLink warning($text, $url, $disabled = false)
 * @method static ButtonLink danger($text, $url, $disabled = false)
 * @method static ButtonLink success($text, $url, $disabled = false)
 *
 * @method ButtonLink text($text)
 * @method ButtonLink type($type)
 * @method ButtonLink url($url)
 * @method ButtonLink disabled($boolean)
 * @method ButtonLink blank($boolean)
 * @method ButtonLink attr($attr)
 *
 * @since 2.1.0
 */
class ButtonLink extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '',
        ];
    }

    /**
     * @param $type string
     * @param $text string
     * @param $url string
     * @return ButtonLink
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
        $methods = ['muted', 'warning', 'danger', 'success', 'primary'];
        if (in_array($name, $methods)) {
            $ins = new static();
            $ins->type($name);
            $ins->text($arguments[0]);
            $ins->url($arguments[1]);
            $ins->disabled(empty($arguments[2]) ? false : true);
            return $ins;
        }
        throw new \Exception('ButtonAjaxRequest error ' . join(',', $methods) . ' ');
    }

    public function render()
    {
        if ($this->disabled) {
            return '<a href="javascript:;" ' . ($this->blank ? 'target="_blank"' : '') . ' class="btn btn-' . $this->type . '" ' . $this->attr . '>' . $this->text . '</a>';
        } else {
            return '<a href="' . $this->url . '" ' . ($this->blank ? 'target="_blank"' : '') . ' class="btn btn-' . $this->type . '" ' . $this->attr . '>' . $this->text . '</a>';
        }
    }

}
