<?php


namespace Module\Vendor\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\TreeUtil;
use ModStart\Module\ModuleManager;
use Module\Vendor\Admin\Widget\AdminWidgetLink;
use Module\Vendor\Type\AdminWidgetLinkType;

class WidgetLinkController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    public function select()
    {
        $input = InputPackage::buildFromInput();
        $filterTypes = $input->getStringSeparatedArray('types');
        if (empty($filterTypes)) {
            $filterTypes = [
                AdminWidgetLinkType::MOBILE,
                AdminWidgetLinkType::WEB,
            ];
        }
        $links = AdminWidgetLink::get();
        $links = array_values(array_filter($links, function ($link) use ($filterTypes) {
            return in_array($link['type'], $filterTypes);
        }));
        $types = [];
        foreach (AdminWidgetLinkType::getList() as $name => $title) {
            if (!in_array($name, $filterTypes)) {
                continue;
            }
            $count = count(array_filter($links, function ($link) use ($name) {
                return $link['type'] == $name;
            }));
            if ($count <= 0) {
                continue;
            }
            $types[] = [
                'name' => $name,
                'title' => $title,
                'count' => $count,
                'icon' => AdminWidgetLinkType::icon($name),
            ];
        }
        return view('modstart::admin.dialog.linkSelector', [
            'types' => $types,
            'links' => $links,
        ]);
    }
}
