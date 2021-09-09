<?php


namespace ModStart\Module;

use Illuminate\Routing\Controller;
use ModStart\Core\View\ResponsiveViewTrait;

class ModuleBaseController extends Controller
{
    use ResponsiveViewTrait;
}