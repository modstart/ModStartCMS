<?php


namespace App\Admin\Controller;


use Illuminate\Routing\Controller;
use Module\Cms\Traits\AdminDashboardTrait;

class IndexController extends Controller
{
    use AdminDashboardTrait;
    public function index()
    {
        return $this->dashboard();
    }

}
