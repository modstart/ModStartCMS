<?php

namespace ModStart\Widget;

use Illuminate\Contracts\Support\Renderable;

/**
 * Class Box
 * @package ModStart\Widget
 *
 * @method Box style($value)
 */
class Box extends AbstractWidget
{
    /**
     * @var string
     */
    protected $view = 'modstart::widget.box';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $classList = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var array
     */
    protected $tools = [];

    /**
     * Box constructor.
     *
     * @param string|Renderable $content
     * @param string $title
     * @param string $classList
     */
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

    /**
     * Set box content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set box title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set box title.
     *
     * @param string $classList
     *
     * @return $this
     */
    public function classList($classList)
    {
        $this->classList = $classList;
        return $this;
    }


    /**
     * @param string|Renderable|\Closure $tool
     *
     * @return $this
     */
    public function tool($tool)
    {
        $this->tools[] = $this->toString($tool);
        return $this;
    }

    /**
     * Variables in view.
     *
     * @return array
     */
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
