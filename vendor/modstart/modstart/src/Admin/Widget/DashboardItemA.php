<?php

namespace ModStart\Admin\Widget;

use ModStart\Core\Util\ColorUtil;
use ModStart\Widget\AbstractWidget;

class DashboardItemA extends AbstractWidget
{
    private $type = 1;
    private $link;
    private $title;
    private $desc;
    private $icon;
    private $color;
    private $number;

    public static function makeIconTitleDesc($icon, $title, $desc, $link = 'javascript:;', $color = '#999')
    {
        $item = new DashboardItemA();
        $item->icon = $icon;
        $item->title = $title;
        $item->desc = $desc;
        $item->link = $link;
        $item->color = $color;
        $item->type = 1;
        return $item;
    }

    public static function makeIconNumberTitle($icon, $number, $title, $link = 'javascript:;', $color = null)
    {
        if (null === $color) {
            $color = ColorUtil::randomColor();
        }
        $item = new DashboardItemA();
        $item->icon = $icon;
        $item->number = $number;
        $item->title = $title;
        $item->link = $link;
        $item->color = $color;
        $item->type = 2;
        return $item;
    }

    public static function makeTitleLink($title, $link)
    {
        $item = new DashboardItemA();
        $item->title = $title;
        $item->link = $link;
        $item->type = 3;
        return $item;
    }

    public static function makeIconNumberTitleDark($icon, $number, $title, $link = 'javascript:;', $color = null)
    {
        if (null === $color) {
            $color = ColorUtil::randomColor();
        }
        $item = new DashboardItemA();
        $item->icon = $icon;
        $item->number = $number;
        $item->title = $title;
        $item->link = $link;
        $item->color = $color;
        $item->type = 4;
        return $item;
    }

    /**
     * @param $icon
     * @param $title
     * @param $link
     * @param null $color
     * @return DashboardItemA
     * @since 1.5.0
     */
    public static function makeIconTitleLink($icon, $title, $link, $color = null)
    {
        if (null === $color) {
            $color = ColorUtil::randomColor();
        }
        $item = new DashboardItemA();
        $item->icon = $icon;
        $item->title = $title;
        $item->link = $link;
        $item->color = $color;
        $item->type = 5;
        return $item;
    }

    public function render()
    {
        switch ($this->type) {
            case 1:
                return <<<HTML
<a href="{$this->link}" class="ub-dashboard-item-a" {$this->formatAttributes()}>
    <div class="icon" style="color:{$this->color}">
        <i class="font {$this->icon}"></i>
    </div>
    <div class="title" style="color:{$this->color}">{$this->title}</div>
    <div class="desc">{$this->desc}</div>
</a>
HTML;
            case 2:
                if (!is_string($this->number)) {
                    $number = number_format($this->number);
                } else {
                    $number = $this->number;
                }
                return <<<HTML
<a href="{$this->link}" class="ub-dashboard-item-a" {$this->formatAttributes()}>
    <div class="icon" style="color:{$this->color}">
        <i class="font {$this->icon}"></i>
    </div>
    <div class="number-value">{$number}</div>
    <div class="number-title">{$this->title}</div>
</a>
HTML;
            case 3:
                return <<<HTML
<a href="{$this->link}" target="_blank" class="btn btn-block ub-text-left margin-bottom" style="color:{$this->color}">
    {$this->title}
</a>
HTML;
            case 4:
                return <<<HTML
<a href="{$this->link}" class="ub-dashboard-item-b" style="background:{$this->color}">
    <div class="icon">
        <i class="font {$this->icon}"></i>
    </div>
    <div class="number-value">{$this->number}</div>
    <div class="number-title">{$this->title}</div>
</a>
HTML;
            case 5:
                return <<<HTML
<a href="{$this->link}" class="tw-block tw-bg-white tw-text-center tw-rounded tw-shadow tw-py-4">
    <div style="height:1.5rem;">
        <i class="{$this->icon}" style="font-size:1.5rem;line-height:1.5rem;color:{$this->color};"></i>
    </div>
    <div class="tw-text-gray-400">{$this->title}</div>
</a>
HTML;


        }

    }

}
