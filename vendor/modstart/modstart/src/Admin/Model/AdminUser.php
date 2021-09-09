<?php


namespace ModStart\Admin\Model;


use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $table = 'admin_user';

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_user_role', 'userId', 'roleId');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (AdminUser $model) {
            $model->roles()->detach();
        });
    }
}