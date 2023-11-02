<?php


namespace ModStart\Widget;


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
 * @method $this attr($attr)
 */
class ButtonDialogRequest extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-button-dialog-request{display:inline-block;}',
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
            $ins->disabled(empty($arguments[2]) ? false : true);
            return $ins->render();
        }
        throw new \Exception('ButtonDialogRequest error ' . join(',', $methods) . ' ');
    }

    /**
     * @param $type string
     * @param $text string
     * @param $url string
     * @return ButtonDialogRequest
     */
    public static function make(...$arguments)
    {
        $ins = new static();
        $ins->type($arguments[0]);
        $ins->text($arguments[1]);
        $ins->url($arguments[2]);
        return $ins;
    }

    public function size($size = 'big')
    {
        switch ($size) {
            case 'big':
                return $this->attr(($this->attr ? $this->attr : '') . ' data-dialog-width="90%" data-dialog-height="90%"');
        }
        return $this;
    }

    public function render()
    {
        if ($this->disabled) {
            return '<a href="javascript:;" class="btn ub-button-dialog-request btn-' . $this->type . '">' . $this->text . '</a>';
        } else {
            return '<a href="javascript:;" ' . ($this->confirm ? 'data-confirm="' . $this->confirm . '"' : '') . ' data-dialog-request="' . $this->url . '" class="btn ub-button-dialog-request btn-' . $this->type . '" ' . ($this->attr ? $this->attr : '') . '>' . $this->text . '</a>';
        }
    }
}
