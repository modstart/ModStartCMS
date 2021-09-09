<?php


namespace ModStart\Admin\Concern;


use ModStart\Admin\Layout\AdminCRUDBuilder;

trait HasAdminQuickCRUD
{
    use HasAdminCRUD;

    protected function grid()
    {
        $builder = new AdminCRUDBuilder();
        $builder->useModeGrid();
        $this->crud($builder);
        return $builder->grid();
    }

    protected function form()
    {
        $builder = new AdminCRUDBuilder();
        $builder->useModeForm();
        $this->crud($builder);
        return $builder->form();
    }

    protected function detail()
    {
        $builder = new AdminCRUDBuilder();
        $builder->useModeDetail();
        $this->crud($builder);
        return $builder->detail();
    }
}
