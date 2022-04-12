<?php


namespace Module\Nav\Render;


use Illuminate\Support\Facades\View;

class NavRender
{
    public static function position($position)
    {
        return View::make('module::Nav.View.inc.nav', [
            'position' => $position,
        ])->render();
    }
}
