<?php


namespace ModStart\Widget\Chart;


use ModStart\Core\Util\RandomUtil;
use ModStart\Widget\AbstractWidget;
use ModStart\Widget\Traits\DSTrait;

class Chart extends AbstractWidget
{
    use DSTrait;

    /**
     * @var string
     */
    protected $view = 'modstart::widget.chart';
    protected $height = 300;
    protected $option = [
        'grid' => [
            // 'top' => '20%',
            'right' => '1%',
            'left' => '1%',
            'bottom' => '10%',
            'containLabel' => true
        ],
        'toolbox' => [
            'feature' => [
                'dataView' => [
                    'show' => true,
                    'readOnly' => false,
                ],
                'restore' => [
                    'show' => true,
                ],
                'saveAsImage' => [
                    'show' => true,
                ],
            ],
        ],
        'tooltip' => [
            'trigger' => 'axis',
            'axisPointer' => [
                'type' => 'shadow',
                'snap' => true,
                'crossStyle' => [
                    'color' => '#999',
                ],
            ],
        ],
        'legend' => [
            'data' => [],
        ],
        'xAxis' => [
            'type' => 'category',
            'data' => []
        ],
        'yAxis' => [
            'type' => 'value',
            'minInterval' => 1
        ],
        'series' => [

        ]
    ];

    /**
     * Chart constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function variables()
    {
        return [
            'option' => $this->option,
            'height' => $this->height,
        ];
    }

    public static function make()
    {
        return new static();
    }

    public function option($value = null)
    {
        if (is_null($value)) {
            return $this->option;
        }
        $this->option = $value;
        return $this;
    }

    public function optionSet($key, $value)
    {
        $this->option[$key] = $value;
        return $this;
    }

    public function random()
    {
        $this->option['xAxis']['data'] = RandomUtil::dateCollection();
        $this->ySeries(0, RandomUtil::numberCollection());
        return $this;
    }

    public function xData($value, $param = [])
    {
        $this->option['xAxis']['data'] = $value;
        return $this;
    }

    public function yData($value, $name = '数量', $param = [])
    {
        return $this->ySeries(0, $value, $name, $param);
    }
}
