<?php

namespace ModStart\Repository;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use ModStart\Form\Form;
use ModStart\Detail\Detail;
use ModStart\Grid\Model;

interface RepositoryInterface
{
    /**
     * 获取主键名称
     *
     * @return string
     */
    public function getKeyName();

    /**
     * 获取创建时间字段
     *
     * @return string
     */
    public function getCreatedAtColumn();

    /**
     * 获取更新时间字段
     *
     * @return string
     */
    public function getUpdatedAtColumn();

    /**
     * 是否使用软删除
     *
     * @return bool
     */
    public function isSoftDeletes();

    /**
     * 获取列表数据
     *
     * @param Model $model
     * @return mixed
     */
    public function get(Model $model);

    /**
     * 增加记录
     *
     * @param Form $form
     *
     * @return mixed
     */
    public function add(Form $form);

    /**
     * 获取编辑的数据
     *
     * @param Form $form
     *
     * @return Arrayable|array
     */
    public function editing(Form $form);

    /**
     * 更新数据
     *
     * @param Form $form
     *
     * @return Arrayable|array
     */
    public function edit(Form $form);

    /**
     * 获取显示记录
     *
     * @param Detail $detail
     *
     * @return Arrayable|array
     */
    public function show(Detail $detail);

    /**
     * 获取删除前的数据
     *
     * @param Form $form
     *
     * @return Arrayable|array
     */
    public function deleting(Form $form);

    /**
     * 删除数据
     *
     * @param Form $form
     * @param Arrayable|array $deletingData
     *
     * @return mixed
     */
    public function delete(Form $form, Arrayable $deletingData);
}
