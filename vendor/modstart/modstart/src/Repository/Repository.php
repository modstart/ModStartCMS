<?php

namespace ModStart\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Grid\Model;
use ModStart\Support\Concern\HasArguments;

abstract class Repository implements RepositoryInterface, SortRepositoryInterface, TreeRepositoryInterface
{
    use Macroable;
    use HasArguments;

    /**
     * @var string
     */
    protected $keyName = 'id';

    /**
     * @var bool
     */
    protected $isSoftDeletes = false;

    /**
     * 获取主键名称.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName ? $this->keyName : 'id';
    }

    /**
     * 设置主键名称.
     *
     * @param string $keyName
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * 获取创建时间字段.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return 'created_at';
    }

    /**
     * 获取更新时间字段.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return 'updated_at';
    }

    /**
     * 是否使用软删除.
     *
     * @return bool
     */
    public function isSoftDeletes()
    {
        return $this->isSoftDeletes;
    }

    /**
     * @param bool $isSoftDeletes
     */
    public function setIsSoftDeletes($isSoftDeletes)
    {
        $this->isSoftDeletes = $isSoftDeletes;
    }

    public function add(Form $form)
    {
        throw new \RuntimeException('This repository does not support "add" method');
    }

    public function editing(Form $form)
    {
        throw new \RuntimeException('This repository does not support "editing" method');
    }

    public function edit(Form $form)
    {
        throw new \RuntimeException('This repository does not support "edit" method');
    }

    public function show(Detail $detail)
    {
        throw new \RuntimeException('This repository does not support "show" method');
    }

    public function deleting(Form $form)
    {
        throw new \RuntimeException('This repository does not support "delete" method');
    }

    public function delete(Form $form, Arrayable $deletingData)
    {
        throw new \RuntimeException('This repository does not support "delete" method');
    }

    /**
     * list items for conditions
     * @param Model $model
     * @return Collection
     */
    public function get(Model $model)
    {
        throw new \RuntimeException('This repository does not support "get" method');
    }

    /**
     * get built model query for conditions
     * @param Model $model
     * @return QueryBuilder
     */
    public function getQuery(Model $model)
    {
        throw new \RuntimeException('This repository dose not support "getQuery" method');
    }

    private $sortColumn = 'sort';

    /**
     * 获取排序字段
     * @return string
     */
    public function getSortColumn()
    {
        return $this->sortColumn;
    }

    /**
     * 设置排序字段
     * @param $value
     */
    public function setSortColumn($value)
    {
        $this->sortColumn = $value;
    }

    public function sortEdit(Form $form)
    {
        throw new RuntimeException('This repository does not support "sortEdit" method.');
    }

    private $treePidColumn = 'pid';
    private $treeTitleColumn = 'title';
    private $treeSortColumn = 'sort';

    public function getTreePidColumn()
    {
        return $this->treePidColumn;
    }

    public function setTreePidColumn($value)
    {
        $this->treePidColumn = $value;
    }

    public function getTreeTitleColumn()
    {
        return $this->treeTitleColumn;
    }


    public function setTreeTitleColumn($value)
    {
        $this->treeTitleColumn = $value;
    }

    public function getTreeSortColumn()
    {
        return $this->treeSortColumn;
    }

    public function setTreeSortColumn($value)
    {
        $this->treeSortColumn = $value;
    }

    public function getTreeItems($context)
    {
        throw new \RuntimeException('This repository does not support "getTreeItems" method.');
    }

    /**
     * @return array
     */
    public function getTreeAncestorItems()
    {
        throw new \RuntimeException('This repository does not support "getTreeAncestorItems" method.');
    }

    /**
     * 构建 Repository
     * @param $params string
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * 根据输入各种输入构建数据仓库
     * @param mixed $repository
     * @param array $args
     * @return Repository
     */
    public static function instance($repository, array $args = [])
    {
        /**
         * 部分场景只需要构建表单
         */
        if (null === $repository) {
            return null;
        }
        if (is_string($repository)) {
            $repository = new $repository($args);
        }
        if ($repository instanceof \Illuminate\Database\Eloquent\Model
            || $repository instanceof \Illuminate\Database\Eloquent\Builder) {
            $repository = EloquentRepository::make($repository);
        }
        if (!$repository instanceof \ModStart\Repository\Repository) {
            $class = is_object($repository) ? get_class($repository) : $repository;
            throw new \InvalidArgumentException("The class [{$class}] must be a type of [" . Repository::class . '].');
        }
        return $repository;
    }
}
