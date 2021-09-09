<?php

namespace ModStart\Layout;

use Illuminate\Contracts\Support\Renderable;
use ModStart\Grid\Grid;

class Column implements Buildable
{

    protected $width = [];

    protected $contents = [];

    public function __construct($content, $width = 12)
    {
        if ($content instanceof \Closure) {
            call_user_func($content, $this);
        } else {
            $this->append($content);
        }

                        if (is_null($width) || (is_array($width) && count($width) === 0)) {
            $this->width['md'] = 12;
        }         elseif (is_numeric($width)) {
            $this->width['md'] = $width;
        } else {
            $this->width = $width;
        }
    }

    
    public function append($content)
    {
        $this->contents[] = $content;

        return $this;
    }

    
    public function row($content)
    {
        if (!$content instanceof \Closure) {
            $row = new Row($content);
        } else {
            $row = new Row();

            call_user_func($content, $row);
        }

        ob_start();

        $row->build();

        $contents = ob_get_contents();

        ob_end_clean();

        return $this->append($contents);
    }

    
    public function build()
    {
        $this->startColumn();

        foreach ($this->contents as $content) {
            if ($content instanceof Renderable || $content instanceof Grid) {
                echo $content->render();
            } else {
                echo (string)$content;
            }
        }

        $this->endColumn();
    }

    
    protected function startColumn()
    {
                $classnName = collect($this->width)->map(function ($value, $key) {
            return "col-$key-$value";
        })->implode(' ');

        echo "<div class=\"{$classnName}\">";
    }

    
    protected function endColumn()
    {
        echo '</div>';
    }
}
