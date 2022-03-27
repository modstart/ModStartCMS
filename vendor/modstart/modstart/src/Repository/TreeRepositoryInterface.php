<?php

namespace ModStart\Repository;

use Illuminate\Support\Collection;
use ModStart\Form\Form;
use ModStart\Grid\Model;

interface TreeRepositoryInterface
{
    /**
     * 获取主键字段名称.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * 获取父级ID字段名称.
     *
     * @return string
     */
    public function getTreePidColumn();

    /**
     * 获取标题字段.
     *
     * @return string
     */
    public function getTreeTitleColumn();

    /**
     * 获取排序字段名称.
     *
     * @return string
     */
    public function getTreeSortColumn();

    /**
     * 获取树状结构
     *
     * @return Collection
     */
    public function getTreeItems($context);

}
