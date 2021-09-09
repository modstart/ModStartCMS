<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;

class UtilController extends Controller
{
    public function frame()
    {
        $input = InputPackage::buildFromInput();
        Session::put('_adminFrameLeftToggle', $input->getBoolean('frameLeftToggle'));
        return Response::jsonSuccess();
    }
}