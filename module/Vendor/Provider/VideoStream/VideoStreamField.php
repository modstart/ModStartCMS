<?php


namespace Module\Vendor\Provider\VideoStream;


use ModStart\Field\AbstractField;
use ModStart\Support\Manager\FieldManager;

class VideoStreamField extends AbstractField
{
    const SCOPE_ADMIN = 'admin';
    const SCOPE_MEMBER = 'member';

    protected $view = 'module::Vendor.View.field.videoStream';
    protected $value = [
        'driver' => null,
        'name' => null,
        'path' => null,
    ];

    protected function setup()
    {
        parent::setup();
        $this->addVariables([
            'scope' => self::SCOPE_ADMIN
        ]);
    }

    public function scope($scope = null)
    {
        if (null == $scope) {
            return $this->getVariable('scope');
        }
        $this->addVariables([
            'scope' => $scope,
        ]);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        $value = json_decode($value, true);
        if (empty($value['driver'])) {
            $value['driver'] = null;
        }
        if (empty($value['name'])) {
            $value['name'] = null;
        }
        if (empty($value['path'])) {
            $value['path'] = null;
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function prepareInput($value, $model)
    {
        return json_decode($value, true);
    }

    public static function register()
    {
        FieldManager::extend('videoStream', VideoStreamField::class);
    }
}
