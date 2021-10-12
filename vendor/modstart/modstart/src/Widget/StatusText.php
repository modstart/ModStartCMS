<?php


namespace ModStart\Widget;


/**
 * Class Label
 * @package ModStart\Widget
 *
 * @method static string muted($text)
 * @method static string warning($text)
 * @method static string danger($text)
 * @method static string success($text)
 *
 * @method void text($text)
 * @method void type($type)
 */
class StatusText extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-status-text{display:inline-block;margin-right:0.5rem;}',
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        $methods = ['muted', 'warning', 'danger', 'success'];
        if (in_array($name, $methods)) {
            $ins = new static();
            $ins->text($arguments[0]);
            $ins->type($name);
            return $ins->render();
        }
        throw new \Exception('StatusText error ' . join(',', $methods) . ' ');
    }

    public function render()
    {
        return '<span class="ub-status-text ub-text-' . $this->type . '">' . $this->text . '</span>';
    }
}