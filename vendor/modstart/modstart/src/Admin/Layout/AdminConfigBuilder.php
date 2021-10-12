<?php


namespace ModStart\Admin\Layout;


use Illuminate\Contracts\Support\Renderable;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Form\Form;
use ModStart\Layout\Page;
use ModStart\Repository\RepositoryUtil;
use ModStart\Widget\Box;

/**
 * Class AdminConfigBuilder
 * @package ModStart\Admin\Layout
 *
 * @mixin Page
 * @mixin Form
 */
class AdminConfigBuilder implements Renderable
{
    /** @var Page */
    private $page;
    /** @var Form */
    private $form;
    private $pagePrepend = [];

    public function __construct()
    {
        $this->form = new Form(DynamicModel::class);
        $this->useFrame();
    }

    public function form()
    {
        return $this->form;
    }

    public function page()
    {
        return $this->page;
    }

    public function useFrame()
    {
        $this->page = new AdminPage();
        $this->form->showReset(false)->showSubmit(true);
    }

    public function useDialog()
    {
        $this->page = new AdminDialogPage();
        $this->form->showReset(false)->showSubmit(false);
    }

    public function pagePrepend($widget)
    {
        array_unshift($this->pagePrepend, $widget);
    }

    public function render()
    {
        if (!empty($this->pagePrepend)) {
            foreach ($this->pagePrepend as $item) {
                $this->page->row($item);
            }
        }
        $this->page->body(new Box($this->form, $this->page->pageTitle()));
        return $this->page->render();
    }

    /**
     * @param \stdClass $item
     * @param \Closure $callback = function (Form $form) { return Response::generateSuccess('ok'); }
     * @return $this
     */
    public function perform($item = null, $callback = null)
    {
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $this->form->formRequest(function (Form $form) use ($callback) {
                if ($callback) {
                    $ret = call_user_func($callback, $form);
                    if (null !== $ret) {
                        return $ret;
                    }
                } else {
                    $config = modstart_config();
                    foreach ($form->dataForming() as $k => $v) {
                        $config->set($k, $v);
                    }
                }
                return Response::jsonSuccess(L('Save Success'));
            });
        }
        if (null === $item) {
            $item = [];
            foreach ($this->form->fields() as $field) {
                $item[$field->column()] = modstart_config($field->column(), $field->defaultValue());
            }
        } else if (false === $item) {
            $item = [];
            foreach ($this->form->fields() as $field) {
                $item[$field->column()] = null;
            }
        }
        $this->form->item(RepositoryUtil::itemFromArray($item));
        $this->form->fillFields();
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->page, $name)) {
            return $this->page->{$name}(...$arguments);
        }
        return $this->form->{$name}(...$arguments);
    }


}