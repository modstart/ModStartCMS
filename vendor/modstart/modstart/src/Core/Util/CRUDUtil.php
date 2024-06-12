<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Grid\Grid;
use ModStart\Grid\Type\GridEngine;

class CRUDUtil
{
    public static function copyId($defaultValue = null)
    {
        $input = InputPackage::buildFromInput();
        return $input->getInteger('_copyId', $defaultValue);
    }

    public static function scope($defaultValue = null)
    {
        $input = InputPackage::buildFromInput();
        return $input->getTrimString('_scope', $defaultValue);
    }

    public static function id()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('_id');
        if (!$id) {
            $id = $input->getInteger('id');
        }
        return $id;
    }

    public static function stringId()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getTrimString('_id');
        if (!$id) {
            $id = $input->getTrimString('id');
        }
        return $id;
    }

    public static function ids()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getTrimString('_id');
        if (!$id) {
            $id = $input->getTrimString('id');
            if (empty($id)) {
                $id = $input->getTrimString('ids');
            }
        }
        $ids = [];
        foreach (explode(',', $id) as $i) {
            $ids[] = intval($i);
        }
        return $ids;
    }

    public static function stringIds()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getTrimString('_id');
        if (!$id) {
            $id = $input->getTrimString('id');
            if (empty($id)) {
                $id = $input->getTrimString('ids');
            }
        }
        $ids = [];
        foreach (explode(',', $id) as $i) {
            $ids[] = $i;
        }
        return $ids;
    }

    public static function registerRouteResource($prefix, $class)
    {
        Route::match(['get', 'post'], "$prefix", "$class@index");
        Route::match(['get', 'post'], "$prefix/add", "$class@add");
        Route::match(['get', 'post'], "$prefix/edit", "$class@edit");
        Route::match(['post'], "$prefix/delete", "$class@delete");
        Route::match(['get'], "$prefix/show", "$class@show");
        Route::match(['post'], "$prefix/sort", "$class@sort");
    }

    public static function registerGridResource(Grid $grid, $class, $param = [])
    {
        if ($grid->canAdd() && ($url = action($class . '@add', $param))) {
            switch ($grid->engine()) {
                case GridEngine::TREE_MASS:
                    $input = InputPackage::buildFromInput();
                    $query = [];
                    $query['_pid'] = $input->get('_pid', $grid->treeRootPid());
                    $grid->urlAdd($url . (strpos($url, '?') > 0 ? '&' : '?') . http_build_query($query));
                    break;
                default:
                    $grid->urlAdd($url);
                    break;
            }
        }
        if ($grid->canEdit() && ($url = action($class . '@edit', $param))) {
            $grid->urlEdit($url);
        }
        if ($grid->canDelete() && ($url = action($class . '@delete', $param))) {
            $grid->urlDelete($url);
        }
        if ($grid->canShow() && ($url = action($class . '@show', $param))) {
            $grid->urlShow($url);
        }
        if ($grid->canSort() && ($url = action($class . '@sort', $param))) {
            $grid->urlSort($url);
        }
        if ($grid->canExport() && ($url = action($class . '@export', $param))) {
            $grid->urlExport($url);
        }
        if ($grid->canImport() && ($url = action($class . '@import', $param))) {
            $grid->urlImport($url);
        }
    }

    public static function jsGridRefresh($index = 0, $flag = 'js')
    {
        return "[${flag}]window.__grids.get($index).lister.refresh();";
    }

    public static function jsDialogCloseAndParentGridRefresh($index = 0, $flag = 'js')
    {
        return "[${flag}]parent.__grids.get($index).lister.refresh();__dialogClose();";
    }

    public static function jsDialogClose($flag = 'js')
    {
        return "[${flag}]__dialogClose();";
    }

    public static function jsDialogCloseAndParentRefresh($flag = 'js')
    {
        return "[${flag}]parent.location.reload();";
    }

    public static function adminRedirectOrTabClose($url)
    {
        $redirect = modstart_admin_url($url);
        if (View::shared('_isTab')) {
            $redirect = '[tab-close]';
        }
        return $redirect;
    }

    public static function adminUrlWithTab($url, $query = [])
    {
        if (View::shared('_isTab')) {
            $query['_is_tab'] = 1;
        }
        return modstart_admin_url($url, $query);
    }

}
