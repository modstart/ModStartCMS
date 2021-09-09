<?php


namespace ModStart\Admin\Model;


use Illuminate\Database\Eloquent\Model;

class AdminRoleRule extends Model
{
    protected $table = 'admin_role_rule';
    protected $fillable = ['rule'];

    public function rules()
    {
        return $this->hasMany(AdminRoleRule::class, 'roleId');
    }

}