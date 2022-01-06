<?php


namespace ModStart\Admin\Layout;


use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Support\Concern\HasFluentAttribute;

/**
 * Class AdminCRUDBuilder
 * @package ModStart\Admin\Layout
 * @mixin Grid|Form|Detail
 *
 * @method AdminCRUDBuilder|mixed field($value = null);
 * @method AdminCRUDBuilder|mixed title($value = null)
 */
class AdminCRUDBuilder
{
    const MODE_GRID = 'grid';
    const MODE_DETAIL = 'detail';
    const MODE_FORM = 'form';

    use HasFluentAttribute;

    private $mode;
    protected $fluentAttributes = [
        'model',
        'field',
    ];
    private $model;
    private $field;
    /** @var Grid */
    private $grid;
    /** @var Form */
    private $form;
    /** @var Detail */
    private $detail;

    public function useModeGrid()
    {
        $this->mode = 'grid';
    }

    public function useModeForm()
    {
        $this->mode = 'form';
    }

    public function useModeDetail()
    {
        $this->mode = 'detail';
    }

    public function init($modelOrTable)
    {
        $this->model = $modelOrTable;
        switch ($this->mode) {
            case 'grid':
                $this->grid = Grid::make($this->model);
                break;
            case 'form':
                $this->form = Form::make($this->model);
                break;
            case 'detail':
                $this->detail = Detail::make($this->model);
                break;
        }
        return $this;
    }

    public function grid()
    {
        $this->grid->builder(function (Grid $grid) {
            if ($this->field) {
                call_user_func($this->field, $grid);
            }
        });
        return $this->grid;
    }

    public function form()
    {
        $this->form->builder(function (Form $form) {
            if ($this->field) {
                call_user_func($this->field, $form);
            }
        });
        return $this->form;
    }

    public function detail()
    {
        $this->detail->builder(function (Detail $detail) {
            if ($this->field) {
                call_user_func($this->field, $detail);
            }
        });
        return $this->detail;
    }

    public function mode()
    {
        return $this->mode;
    }

    public function __call($name, $arguments)
    {
        if ($this->isFluentAttribute($name)) {
            return $this->fluentAttribute($name, $arguments);
        }
        switch ($this->mode) {
            case 'grid':
                return call_user_func_array([$this->grid, $name], $arguments);
            case 'form':
                return call_user_func_array([$this->form, $name], $arguments);
            case 'detail':
                return call_user_func_array([$this->detail, $name], $arguments);
        }
        throw new \Exception('AdminCRUDBuilder call error : ' . $name);
    }

}
