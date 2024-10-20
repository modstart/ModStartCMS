<?php


namespace ModStart\Field;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Field\Concern\CanCascadeFields;
use ModStart\Field\Type\FieldRenderMode;

class SelectRemote extends AbstractField
{
    use CanCascadeFields;

    protected function setup()
    {
        $this->addVariables([
            'server' => '',
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    /**
     * 动态显示模型数据
     * @param $table string 模型表名
     * @param $valueKey string 模型主键，默认为id
     * @param $labelKey string 模型显示字段，默认为title
     * @return $this
     */
    public function viewWithModel($table, $valueKey = 'id', $labelKey = 'title')
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) use ($table, $valueKey, $labelKey) {
            switch ($field->renderMode()) {
                case FieldRenderMode::DETAIL:
                case FieldRenderMode::GRID:
                    $value = $item[$field->name()];
                    $record = ModelUtil::get($table, [$valueKey => $value]);
                    if ($record) {
                        $value = $record[$labelKey];
                    }
                    return AutoRenderedFieldValue::make($value);
            }
        });
        return $this;
    }

    /**
     * @param $table string 模型表名
     * @param $valueKey string 模型主键，默认为id
     * @param $labelKey string 模型显示字段，默认为title
     * @param $param array 查询参数
     * @return array
     */
    public static function handleModel($table, $valueKey = 'id', $labelKey = 'title', $param = [])
    {
        if (!isset($param['sort'])) {
            $param['sort'] = ['id', 'desc'];
        }
        $input = InputPackage::buildFromInput();
        $value = $input->getInteger('value');
        $keywords = $input->getTrimString('keywords');
        $query = ModelUtil::model($table);
        if ($value) {
            $query = $query->where($valueKey, $value);
        }
        if ($keywords) {
            $query = $query->where($labelKey, 'like', '%' . $keywords . '%');
        }
        $records = $query
            ->orderBy($param['sort'][0], $param['sort'][1])
            ->limit(10)
            ->get([$valueKey, $labelKey])->toArray();
        $options = [];
        foreach ($records as $record) {
            $options[] = [
                'value' => $record[$valueKey],
                'label' => $record[$labelKey],
            ];
        }
        return Response::generateSuccessData([
            'options' => $options,
        ]);
    }

}
