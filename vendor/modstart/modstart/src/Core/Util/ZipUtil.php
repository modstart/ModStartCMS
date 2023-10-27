<?php


namespace ModStart\Core\Util;


use Chumper\Zipper\Zipper;
use Illuminate\Support\Str;

class ZipUtil
{
    private static function fileTreeBuild($files)
    {
        $tree = [];
        foreach ($files as $file) {
            $parts = explode('/', $file['path']);
            $currentLevel = &$tree;
            $partCounts = count($parts);
            foreach ($parts as $partIndex => $part) {
                $found = false;
                foreach ($currentLevel as &$node) {
                    if ($node['name'] === $part) {
                        $currentLevel = &$node['_child'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $isFile = ($partIndex === $partCounts - 1);
                    $newNode = ['name' => $part];
                    if ($isFile) {
                        $newNode['type'] = strtolower(pathinfo($part, PATHINFO_EXTENSION));
                        $newNode['size'] = $file['size'];
                    } else {
                        $newNode['_child'] = [];
                    }
                    $currentLevel[] = $newNode;
                    if (!$isFile) {
                        $currentLevel = &$currentLevel[count($currentLevel) - 1]['_child'];
                    }
                }
            }
        }

        return $tree;
    }

    private static function fileTreeFilter($tree, $filter, $depth = 0)
    {
        $newTree = [];
        foreach ($tree as $node) {
            if (call_user_func_array($filter, [$node, $depth])) {
                $newNode = $node;
                if (isset($node['_child'])) {
                    $newNode['_child'] = self::fileTreeFilter($node['_child'], $filter, $depth + 1);
                }
                $newTree[] = $newNode;
            }
        }
        return $newTree;
    }

    private static function fileTreeSortNodes(&$nodes)
    {
        usort($nodes, function ($a, $b) {
            if (isset($a['type']) && isset($b['type'])) {
                // file
                return strcmp($a['name'], $b['name']);
            } else if (isset($a['type'])) {
                // file
                return 1;
            } else if (isset($b['type'])) {
                // file
                return -1;
            }
            // dir
            return strcmp($a['name'], $b['name']);
        });
        return $nodes;
    }

    private static function fileTreeSort(&$tree)
    {
        foreach ($tree as &$node) {
            if (isset($node['_child'])) {
                self::fileTreeSort($node['_child']);
            }
        }
        self::fileTreeSortNodes($tree);
        return $tree;
    }

    public static function fileTree($path, $option = [])
    {
        if (!isset($option['ignoreSystem'])) {
            $option['ignoreSystem'] = true;
        }
        if (!isset($option['ignoreHidden'])) {
            $option['ignoreHidden'] = true;
        }
        if (!isset($option['maxDepth'])) {
            $option['maxDepth'] = 0;
        }
        if (!isset($option['detectRoot'])) {
            $option['detectRoot'] = false;
        }
        $zipper = new Zipper();
        $zipper->make($path);
        $files = [];
        $zipper->getRepository()->each(function ($name, $stat) use (&$files) {
            $files[] = [
                'path' => str_replace('\\', '/', $name),
                'size' => $stat['size'],
            ];
        });
        $zipper->close();
        $files = ArrayUtil::sortByKey($files, 'path');
        $tree = self::fileTreeBuild($files);
        if ($option['ignoreSystem']) {
            $tree = self::fileTreeFilter($tree, function ($node, $depth) {
                if (isset($node['type'])) {
                    // file
                    if (in_array($node['type'], ['DS_Store',])) {
                        return false;
                    }
                } else {
                    // dir
                    if (in_array($node['name'], ['__MACOSX',])) {
                        return false;
                    }
                }
                return true;
            });
        }
        if ($option['ignoreHidden']) {
            $tree = self::fileTreeFilter($tree, function ($node, $depth) {
                if (Str::startsWith($node['name'], '.')) {
                    return false;
                }
                return true;
            });
        }
        if ($option['detectRoot']) {
            $root = $tree;
            for (; ;) {
                if (count($root) > 1) {
                    break;
                }
                if (!isset($root[0]['_child'])) {
                    break;
                }
                $root = $root[0]['_child'];
            }
            $tree = $root;
        }
        if ($option['maxDepth'] > 0) {
            $tree = self::fileTreeFilter($tree, function ($node, $depth) use ($option) {
                return $depth < $option['maxDepth'];
            });
        }
        return self::fileTreeSort($tree);
    }
}
