<?php


namespace Module\Vendor\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Module\ModuleManager;
use Module\Vendor\Admin\Widget\AdminWidgetLink;

class WidgetLinkController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    private function build($groupName, $titleLinks)
    {
        if (empty($titleLinks)) {
            return null;
        }
        return [
            'title' => $groupName,
            'list' => array_filter(array_map(function ($item) {
                return $item ? [
                    'title' => $item[0],
                    'link' => $item[1],
                ] : null;
            }, $titleLinks))
        ];
    }

    public function select()
    {
        return view('modstart::admin.dialog.linkSelector', [
            'links' => AdminWidgetLink::get(),
        ]);
    }
}
