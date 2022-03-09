<?php


namespace App\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminGrid;
use ModStart\Support\Concern\HasPageTitleInfo;

class GridTestController extends Controller
{
    use HasPageTitleInfo;
    use HasAdminGrid;

    public function grid()
    {

    }
}