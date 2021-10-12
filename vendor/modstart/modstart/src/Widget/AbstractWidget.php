<?php

namespace ModStart\Widget;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;
use ModStart\Core\Util\ConvertUtil;
use ModStart\Core\Util\IdUtil;

/**
 * Class AbstractWidget
 * @package ModStart\Widget
 */
class AbstractWidget extends Fluent implements Renderable
{
    protected $view;
    protected $id;

    /**
     * AbstractWidget constructor.
     */
    public function __construct()
    {
        $this->id = IdUtil::generate('Widget');
    }


    protected function formatAttributes()
    {
        $html = [];
        foreach ((array)$this->getAttributes() as $key => $value) {
            $element = $this->formatAttribute($key, $value);
            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    private function formatAttribute($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }
        if (!is_null($value)) {
            return $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '"';
        }
    }

    protected function variables()
    {
        return [];
    }

    protected function toString($value)
    {
        return ConvertUtil::render($value);
    }

    public function render()
    {
        $data = array_merge([
            'id' => $this->id,
            'attributes' => $this->formatAttributes(),
        ], $this->variables());
        return view($this->view, $data)->render();
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
