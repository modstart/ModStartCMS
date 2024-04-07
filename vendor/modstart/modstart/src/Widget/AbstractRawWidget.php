<?php

namespace ModStart\Widget;

use ModStart\Core\Util\IdUtil;

/**
 * Class Box
 * @package ModStart\Widget
 */
abstract class AbstractRawWidget extends AbstractWidget
{
    /**
     * @var string
     */
    protected $view = 'modstart::widget.raw';

    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->id = IdUtil::generate('RawWidget');
        self::init();
    }


    /**
     * Variables in view.
     *
     * @return array
     */
    public function variables()
    {
        return [
            'id' => $this->id,
            'name' => get_class($this),
            'content' => $this->toString($this->content()),
        ];
    }

    public function id()
    {
        return $this->id;
    }

    protected function init()
    {

    }

    abstract public function content();
}
