<?php

namespace ModStart\Widget;

/**
 * Class Box
 * @package ModStart\Widget
 */
class Nav extends AbstractWidget
{
    /**
     * @var string
     */
    protected $view = 'modstart::widget.nav';

    /**
     * @var string
     */
    protected $navs = '';

    /**
     * @var string
     */
    protected $classList = '';

    /**
     * @var string
     */
    protected $attributes = '';

    /**
     * Box constructor.
     *
     * @param array $navs
     */
    public function __construct($navs, $classList = '')
    {
        parent::__construct();
        $this->navs = $navs;
        $this->classList = $classList;
    }

    public static function make($navs = [], $classList = '')
    {
        return new static($navs, $classList);
    }

    public function navs($navs)
    {
        $this->navs = $navs;
        return $this;
    }

    public function classList($classList)
    {
        $this->classList = $classList;
        return $this;
    }

    public function attributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function append($title, $url, $active = false)
    {
        $this->navs[] = [
            'title' => $title,
            'url' => $url,
            'active' => $active,
        ];
        return $this;
    }

    public function appendDialog($title, $url, $active = false, $width = null, $height = null)
    {
        $this->navs[] = [
            'title' => $title,
            'dialog' => [
                'url' => $url,
                'width' => $width,
                'height' => $height,
            ],
            'active' => $active,
        ];
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
            'navs' => $this->navs,
            'classList' => $this->classList,
        ];
    }
}
