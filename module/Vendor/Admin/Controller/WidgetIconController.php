<?php


namespace Module\Vendor\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;

class WidgetIconController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    public function index()
    {
        $icons = [];

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
            'title' => '内置图标',
            'list' => $list,
        ];

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
            'title' => 'Font Awesome',
            'list' => $list,
        ];

        return Response::jsonSuccessData($icons);
    }
}
