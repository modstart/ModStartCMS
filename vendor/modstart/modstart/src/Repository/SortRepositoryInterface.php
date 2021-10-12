<?php

namespace ModStart\Repository;

use ModStart\Form\Form;

interface SortRepositoryInterface
{
    /**
     * 获取主键字段名称.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * 获取排序字段名称.
     *
     * @return string
     */
    public function getSortColumn();

    /**
     * 保存排序
     * @param Form $form
     * @return mixed
     */
    public function sortEdit(Form $form);
}
