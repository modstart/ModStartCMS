<?php


namespace ModStart\App\Web\Layout;


use Illuminate\Contracts\Support\Renderable;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Form\Form;
use ModStart\Layout\Page;
use ModStart\Repository\RepositoryUtil;
use ModStart\Widget\Box;


class WebConfigBuilder implements Renderable
{
    
    private $page;
    
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
        $this->page = new WebPage();
        $this->form->showReset(false)->showSubmit(true);
    }

    public function useDialog()
    {
        $this->page = new WebDialogPage();
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
        if ($this->page instanceof WebDialogPage) {
            $this->page->body($this->form);
        } else {
            $this->page->body(new Box($this->form, $this->pageTitle()));
        }
        return $this->page->render();
    }

    
    public function perform($item = null, $callback = null)
    {
        if (Request::isPost()) {
            return $this->form->formRequest(function (Form $form) use ($callback) {
                $ret = call_user_func($callback, $form);
                if (null !== $ret) {
                    return $ret;
                }
                return Response::jsonSuccess(L('Save Success'));
            });
        }
        if (null === $item) {
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
