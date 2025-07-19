<?php


namespace Module\Vendor\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;

class WidgetIconController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    public function index()
    {
        $input = InputPackage::buildFromInput();
        $scope = $input->getStringSeparatedArray('scope');
        if (empty($scope)) {
            $scope = ['base', 'fa'];
        }

        $icons = [];

        if (in_array('base', $scope)) {
            $list = [];
            if (file_exists($file = public_path('asset/vendor/iconfont/iconfont.css')) && ($content = file_get_contents($file))) {
                preg_match_all('/\\.icon-([a-z0-9\\-]+):before/', $content, $mat);
                $list = array_map(function ($title) {
                    return [
                        'title' => $title,
                        'cls' => "iconfont icon-$title",
                    ];
                }, $mat[1]);
            }
            $icons[] = [
                'name' => 'iconfont',
                'title' => '内置图标',
                'list' => $list,
            ];
        }

        if (in_array('fa', $scope)) {
            $list = [];
            if (file_exists($file = public_path('asset/font-awesome/css/font-awesome.min.css')) && ($content = file_get_contents($file))) {
                preg_match_all('/\\.fa-([a-z0-9\\-]+):before/', $content, $mat);
                $list = array_map(function ($title) {
                    return [
                        'title' => $title,
                        'cls' => "fa fa-$title",
                    ];
                }, $mat[1]);
            }
            $icons[] = [
                'name' => 'font-awesome',
                'title' => 'Font Awesome',
                'list' => $list,
            ];
        }

        return Response::jsonSuccessData($icons);
    }
}
