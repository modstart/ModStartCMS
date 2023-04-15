<?php


namespace ModStart\Grid\Filter\Field;


use ModStart\Grid\Filter\AbstractFilter;
use ModStart\Support\Concern\HasFluentAttribute;

/**
 * Class AbstractFilterField
 * @package ModStart\Grid\Filter\Field
 *
 * @method AbstractFilterField label($value = null)
 */
abstract class AbstractFilterField
{
    use HasFluentAttribute;

    private $fluentAttributes = [];
    protected $label = '';
    /** @var AbstractFilter */
    private $filter;

    /**
     * AbstractFilterField constructor.
     */
    public function __construct(AbstractFilter $filter)
    {
        $this->filter = $filter;
        $this->setup();
    }

    protected function setup()
    {
    }

    public function label($value = null)
    {
        if (is_null($value)) {
            return $this->label;
        }
        $this->label = $value;
        return $this;
    }

    public function variables()
    {
        $variables = [];
        foreach ($this->fluentAttributes as $k) {
            $variables[$k] = $this->{$k};
        }
        return $variables;
    }

    public function __call($method, $arguments)
    {
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        throw new \Exception('AbstractFilterField __call error : ' . $method);
    }


}
