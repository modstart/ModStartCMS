<?php


namespace Module\Nav\View;


use Illuminate\Support\Facades\View;

class NavView
{
    public static function position($position)
    {
        return View::make('module::Nav.View.inc.nav', [
            'position' => $position,
        ])->render();
    }
}
