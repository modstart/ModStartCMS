<?php

namespace Module\VisitStatistic\View;

use Illuminate\Support\Facades\View;

class VisitStatisticRender
{
    public static function tick()
    {
        return View::make('module::VisitStatistic.View.inc.tick', [
        ])->render();
    }
}
