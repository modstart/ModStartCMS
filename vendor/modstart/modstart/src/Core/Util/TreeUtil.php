<?php

namespace ModStart\Core\Util;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ModStart\Core\Dao\ModelUtil;

class TreeUtil
{
    static $CHILD_KEY = '_child';

    public static function setChildKey($key)
    {
        self::$CHILD_KEY = $key;
    }

    /**
     * @param $model
     * @param array $fieldsMap $fieldsMap = [title=>titleField,...]
     * @param string $keyId
     * @param string $keyPid
     * @param string $keySort
     * @param array $where
     * @return array
     */
    public static function modelToTree($model, $fieldsMap = [], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort', $where = [])
    {
        $models = ModelUtil::all($model, $where);
        $nodes = [];
        foreach ($models as &$model) {
            $node = [];
            $node[$keyId] = $model[$keyId];
            $node[$keyPid] = $model[$keyPid];
            $node[$keySort] = $model[$keySort];
            foreach ($fieldsMap as $k => $v) {
                if (is_numeric($k)) {
                    $node[$v] = $model[$v];
                } else {
                    $node[$k] = $model[$v];
                }
            }
            $nodes[] = $node;
        }
        return self::nodesToTree($nodes, 0, $keyId, $keyPid, $keySort);
    }

    /**
     * 读取模型节点，组装成为一个树
     * @param $pid
     * @param $model
     * @param array $fieldsMap
     * @param string $keyId
     * @param string $keyPid
     * @param string $keySort
     * @return array
     */
    public static function modelToTreeByParentId($pid, $model, $fieldsMap = [], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort')
    {
        $models = [];

        $m = ModelUtil::get($model, [$keyId => $pid]);
        if (empty($m)) {
            return [];
        }
        $topPid = $m[$keyPid];
        $models[] = $m;

        $ms = ModelUtil::all($model, [$keyPid => $pid]);
        do {
            $parentIds = [];
            foreach ($ms as &$m) {
                $parentIds[] = $m[$keyId];
                $models[] = $m;
            }
            if (empty($parentIds)) {
                $ms = null;
            } else {
                $ms = ModelUtil::model($model)->whereIn($keyPid, $parentIds)->get()->toArray();
            }
        } while (!empty($ms));

        $nodes = [];
        foreach ($models as &$model) {
            $node = [];
            $node[$keyId] = $model[$keyId];
            $node[$keyPid] = $model[$keyPid];
            $node[$keySort] = $model[$keySort];
            foreach ($fieldsMap as $k => $v) {
                $node[$k] = $model[$v];
            }
            $nodes[] = $node;
        }
        return self::nodesToTree($nodes, $topPid, $keyId, $keyPid, $keySort);
    }

    /**
     * 检测模型节点是否能够删除，以下情形不能删除
     * - 如果有子节点则不能删除
     * @param $model
     * @param $id
     * @param string $pidName
     * @param array $where
     * @return bool
     */
    public static function modelNodeDeleteAble($model, $id, $pidName = 'pid', $where = [])
    {
        return !ModelUtil::exists($model, array_merge($where, [$pidName => $id]));
    }

    /**
     * 建的模型节点是否能够修改，以下情况不能修改
     * - 如果修改后变成一个环
     * - 如果修改后节点断裂
     * @param $model
     * @param $id
     * @param $fromPid
     * @param $toPid
     * @param string $idName
     * @param string $pidName
     * @param array $where
     * @return bool
     */
    public static function modelNodeChangeAble($model, $id, $fromPid, $toPid, $idName = 'id', $pidName = 'pid', $where = [])
    {
        if ($fromPid == $toPid) {
            return true;
        }

        $_toPid = $toPid;

        while ($m = ModelUtil::get($model, array_merge($where, [$idName => $_toPid]))) {
            if ($m[$idName] == $id) {
                return false;
            }
            $_toPid = $m[$pidName];
        }

        return true;
    }

    public static function nodesToTree(&$nodes, $pid = 0, $idName = 'id', $pidName = 'pid', $sortName = 'sort', $sortDirection = 'asc')
    {
        if ($sortName && $sortDirection) {
            $nodes = ArrayUtil::sortByKey($nodes, $sortName, $sortDirection);
        }
        $items = [];
        foreach ($nodes as $v) {
            $items[$v[$idName]] = $v;
        }
        $tree = [];
        foreach ($items as $item) {
            if (isset($items[$item[$pidName]])) {
                $items[$item[$pidName]][self::$CHILD_KEY][] = &$items[$item[$idName]];
            } else {
                $tree[] = &$items[$item[$idName]];
            }
        }
        return $tree;
    }

    public static function treeToListWithIndent(&$tree, $keyId = 'id', $keyTitle = 'title', $level = 0, $keepKeys = [])
    {
        $options = array();
        foreach ($tree as &$r) {
            $item = array(
                'id' => $r[$keyId],
                'title' => str_repeat('├', $level) . htmlspecialchars($r[$keyTitle]),
            );
            if (!empty($keepKeys)) {
                foreach ($keepKeys as $k) {
                    $item[$k] = $r[$k];
                }
            }
            $options[] = $item;
            if (!empty($r[self::$CHILD_KEY])) {
                $options = array_merge($options, self::treeToListWithIndent($r[self::$CHILD_KEY], $keyId, $keyTitle, $level + 1, $keepKeys));
            }
        }
        return $options;
    }

    public static function treeToListWithLevel(&$tree, $keyId = 'id', $keyTitle = 'title', $keyPid = 'pid', $level = 0)
    {
        $options = array();
        foreach ($tree as &$r) {
            $options[] = array('id' => $r[$keyId], 'title' => $r[$keyTitle], 'level' => $level, 'pid' => $r[$keyPid],);
            if (!empty($r[self::$CHILD_KEY])) {
                $options = array_merge($options, self::treeToListWithLevel($r[self::$CHILD_KEY], $keyId, $keyTitle, $keyPid, $level + 1));
            }
        }
        return $options;
    }

    public static function nodesChildrenIds(&$nodes, $id, $pk_name = 'id', $pid_name = 'pid')
    {
        $ids = [];
        foreach ($nodes as &$li) {
            if ($li[$pid_name] == $id) {
                $ids[] = $li[$pk_name];
                $childIds = self::nodesChildrenIds($nodes, $li[$pk_name], $pk_name, $pid_name);
                if (!empty($childIds)) {
                    $ids = array_merge($ids, $childIds);
                }
            }
        }
        return $ids;
    }

    public static function treeChain(&$tree, $id, $pk_name = 'id', $pid_name = 'pid', $chain = [])
    {
        foreach ($tree as $item) {
            if ($item[$pk_name] == $id) {
                $chain[] = $item;
                return $chain;
            }
            if (!empty($item[self::$CHILD_KEY])) {
                $chain[] = ArrayUtil::removeKeys($item, [self::$CHILD_KEY]);
                $results = self::treeChain($item[self::$CHILD_KEY], $id, $pk_name, $pid_name, $chain);
                if (!empty($results)) {
                    return $results;
                }
            }
        }
        return [];
    }

    public static function nodesChain(&$nodes, $id, $pk_name = 'id', $pid_name = 'pid')
    {
        $chain = [];
        $limit = 0;
        $found = true;
        while ($found && $limit++ < 999) {
            $found = false;
            foreach ($nodes as $li) {
                if ($li[$pk_name] == $id) {
                    $found = true;
                    $id = $li[$pid_name];
                    $chain[] = $li;
                    break;
                }
            }
        }
        return array_reverse($chain);
    }

    public static function itemsMergeLevel($items, $idName = 'id', $pidName = 'pid', $sortName = 'sort', $pid = 0, $level = 1, $newItems = null)
    {
        if (!($items instanceof Collection)) {
            $items = collect($items);
        }
        if ($level == 1) {
            $items = $items->sortBy($sortName)->values();
        }
        if (null === $newItems) {
            $newItems = collect();
        }
        $items->each(function ($item) use ($idName, $pidName, $sortName, $pid, $level, $items, $newItems) {
            if ($item instanceof Model) {
                if (!isset($item->_level)) {
                    if ($item->{$pidName} == $pid) {
                        $item->_level = $level;
                        $newItems->push($item);
                        if ($level < 99) {
                            self::itemsMergeLevel($items, $idName, $pidName, $sortName, $item->{$idName}, $level + 1, $newItems);
                        }
                    }
                }
            } else {
                if (!property_exists($item, '_level')) {
                    if ($item->{$pidName} == $pid) {
                        $item->_level = $level;
                        $newItems->push($item);
                        if ($level < 99) {
                            self::itemsMergeLevel($items, $idName, $pidName, $sortName, $item->{$idName}, $level + 1, $newItems);
                        }
                    }
                }
            }
        });
        return $newItems;
    }

    /**
     * 检测模型节点是否能增加
     * - 如果有子节点则不能删除
     * @param Model $model
     * @param $id
     * @param string $idName
     * @param array $where
     * @return bool
     */
    public static function modelItemAddAble(Model $model, $pid, $idName = 'id')
    {
        return $model->newQuery()->where([$idName => $pid])->exists();
    }

    /**
     * 检测模型节点是否能够删除，以下情形不能删除
     * - 如果有子节点则不能删除
     * @param Model $model
     * @param $id
     * @param string $pidName
     * @param array $where
     * @return bool
     */
    public static function modelItemDeleteAble(Model $model, $id, $pidName = 'pid')
    {
        return !$model->newQuery()->where([$pidName => $id])->exists();
    }

    /**
     * 建的模型节点是否能够修改，以下情况不能修改
     * - 如果修改后变成一个环
     * - 如果修改后节点断裂
     * @param $model
     * @param $id
     * @param $fromPid
     * @param $toPid
     * @param string $idName
     * @param string $pidName
     * @param array $where
     * @return bool
     */
    public static function modelItemChangeAble(Model $model, $id, $fromPid, $toPid, $idName = 'id', $pidName = 'pid')
    {
        if ($fromPid == $toPid) {
            return true;
        }
        $columns = [$idName, $pidName];
        $_toPid = $toPid;
        while ($m = $model->newQuery()->where([$idName => $_toPid])->first($columns)) {
            if ($m->{$idName} == $id) {
                return false;
            }
            $_toPid = $m->{$pidName};
        }
        return true;
    }

}
