<?php

namespace Module\MemberOauth\Admin\Controller;

use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function index()
    {

        return view('module::MemberOauth.View.admin.index');
    }
}
