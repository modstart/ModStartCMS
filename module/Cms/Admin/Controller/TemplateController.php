<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;

class TemplateController extends Controller
{
    public function index()
    {
        return view('module::Cms.View.admin.template.index');
    }
}
