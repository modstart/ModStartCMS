<?php


namespace ModStart\Admin\Model;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_log';

    public function data()
    {
        return self::hasOne(AdminLogData::class, 'id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (AdminLog $model) {
            $model->data()->delete();
        });
    }
}
