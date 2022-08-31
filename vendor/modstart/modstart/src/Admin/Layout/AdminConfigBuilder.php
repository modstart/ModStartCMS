<?php


namespace ModStart\Admin\Layout;


use Illuminate\Contracts\Support\Renderable;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Field\AbstractField;
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
 * @method $this disableBoxWrap($enable)
 */
class AdminConfigBuilder implements Renderable
{
    /** @var Page */
    private $page;
    /** @var Form */
    private $form;
    private $pagePrepend = [];
    private $config = [
        'disableBoxWrap' => false,
    ];

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
        $body = $this->form;
        if (!$this->config['disableBoxWrap']) {
            $body = new Box($this->form, $this->page->pageTitle());
        }
        $this->page->body($body);
        return $this->page->render();
    }

    /**
     * @param \stdClass|null|false $item null表示使用默认的modstart_config配置获取，false表示不使用任何内容初始化
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
                /** @var $field AbstractField */
                if ($field->isLayoutField()) {
                    continue;
                }
                $item[$field->column()] = modstart_config($field->column(), $field->defaultValue());
            }
        } else if (false === $item) {
            $item = [];
            foreach ($this->form->fields() as $field) {
                /** @var $field AbstractField */
                if ($field->isLayoutField()) {
                    continue;
                }
                $item[$field->column()] = null;
            }
        }
        $this->form->item(RepositoryUtil::itemFromArray($item));
        $this->form->fillFields();
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $arguments[0];
            return $this;
        }
        if (method_exists($this->page, $name)) {
            return $this->page->{$name}(...$arguments);
        }
        return $this->form->{$name}(...$arguments);
    }


}
