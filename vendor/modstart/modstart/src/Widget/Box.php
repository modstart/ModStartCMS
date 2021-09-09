<?php

namespace ModStart\Widget;

use Illuminate\Contracts\Support\Renderable;


class Box extends AbstractWidget
{
    
    protected $view = 'modstart::widget.box';

    
    protected $title = '';

    
    protected $classList = '';

    
    protected $content = '';

    
    protected $tools = [];

    
    public function __construct($content, $title = '', $classList = '')
    {
        parent::__construct();
        if ($content) {
            $this->content($content);
        }
        if ($title) {
            $this->title($title);
        }
        if ($classList) {
            $this->classList($classList);
        }
    }

    public static function make($content, $title = '', $classList = '')
    {
        return new Box($content, $title, $classList);
    }

    
    public function content($content)
    {
        $this->content = $content;
        return $this;
    }

    
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    
    public function classList($classList)
    {
        $this->classList = $classList;
        return $this;
    }


    
    public function tool($tool)
    {
        $this->tools[] = $this->toString($tool);
        return $this;
    }

    
    public function variables()
    {
        return [
            'title' => $this->title,
            'classList' => $this->classList,
            'content' => $this->toString($this->content),
            'tools' => $this->tools,
        ];
    }
}
