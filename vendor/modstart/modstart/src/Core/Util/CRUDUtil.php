<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Grid\Grid;
use ModStart\Grid\Type\GridEngine;

/**
 * @Util CRUD 操作工具
 */
class CRUDUtil
{
    /**
     * @Util 获取复制源记录 ID
     * @param $defaultValue mixed 默认值
     * @return int|null
     */
    public static function copyId($defaultValue = null)
    {
        $input = InputPackage::buildFromInput();
        return $input->getInteger('_copyId', $defaultValue);
    }

    /**
     * @Util 获取当前列表作用域标识
     * @param $defaultValue mixed 默认值
     * @return string|null
     */
    public static function scope($defaultValue = null)
    {
        $input = InputPackage::buildFromInput();
        return $input->getTrimString('_scope', $defaultValue);
    }

    /**
     * @Util 获取当前操作记录的整数 ID
     * @return int
     */
    public static function id()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('_id');
        if (!$id) {
            $id = $input->getInteger('id');
        }
        return $id;
    }

    /**
     * @Util 获取当前操作记录的字符串 ID
     * @return string
     */
    public static function stringId()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getTrimString('_id');
        if (!$id) {
            $id = $input->getTrimString('id');
        }
        return $id;
    }

    /**
     * @Util 获取当前操作记录的整数 ID 数组
     * @return array
     */
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

    /**
     * @Util 获取当前操作记录的字符串 ID 数组
     * @return array
     */
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

    /**
     * @Util 注册标准 CRUD 路由（index/add/edit/delete/show/sort）
     * @param $prefix string 路由前缀
     * @param $class string 控制器类名
     * @return void
     */
    public static function registerRouteResource($prefix, $class)
    {
        Route::match(['get', 'post'], "$prefix", "$class@index");
        Route::match(['get', 'post'], "$prefix/add", "$class@add");
        Route::match(['get', 'post'], "$prefix/edit", "$class@edit");
        Route::match(['post'], "$prefix/delete", "$class@delete");
        Route::match(['get'], "$prefix/show", "$class@show");
        Route::match(['post'], "$prefix/sort", "$class@sort");
    }

    /**
     * @Util 根据 Grid 配置自动注册 CRUD URL
     * @param $grid Grid Grid 对象
     * @param $class string 控制器类名
     * @param $param array 额外参数
     * @return void
     */
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

    /**
     * @Util 生成刷新指定 Grid 的 JS 指令
     * @param $index int Grid 索引
     * @param $flag string 指令标识前缀
     * @return string
     */
    public static function jsGridRefresh($index = 0, $flag = 'js')
    {
        return "[${flag}]window.__grids.get($index).lister.refresh();";
    }

    /**
     * @Util 生成关闭弹框并刷新父 Grid 的 JS 指令
     * @param $index int Grid 索引
     * @param $flag string 指令标识前缀
     * @return string
     */
    public static function jsDialogCloseAndParentGridRefresh($index = 0, $flag = 'js')
    {
        return "[${flag}]parent.__grids.get($index).lister.refresh();__dialogClose();";
    }

    /**
     * @Util 生成关闭弹框的 JS 指令
     * @param $flag string 指令标识前缀
     * @return string
     */
    public static function jsDialogClose($flag = 'js')
    {
        return "[${flag}]__dialogClose();";
    }

    /**
     * @Util 生成关闭弹框并刷新父页的 JS 指令
     * @param $flag string 指令标识前缀
     * @return string
     */
    public static function jsDialogCloseAndParentRefresh($flag = 'js')
    {
        return "[${flag}]parent.location.reload();";
    }

    /**
     * @Util 获取后台重定向或 Tab 关闭的返回地址（Tab 下自动关闭）
     * @param $url string 目标 URL
     * @return string
     */
    public static function adminRedirectOrTabClose($url)
    {
        if (strpos($url, '[js]') === 0) {
            $redirect = $url;
        } else {
            $redirect = modstart_admin_url($url);
        }
        if (View::shared('_isTab')) {
            $redirect = '[tab-close]';
        }
        return $redirect;
    }

    /**
     * @Util 生成带 Tab 参数的后台 URL
     * @param $url string URL 路径
     * @param $query array 额外查询参数
     * @param $forceTab bool 是否强制为 Tab 模式
     * @return string
     */
    public static function adminUrlWithTab($url, $query = [], $forceTab = false)
    {
        if ($forceTab || View::shared('_isTab')) {
            $query['_is_tab'] = 1;
        }
        return modstart_admin_url($url, $query);
    }

}
