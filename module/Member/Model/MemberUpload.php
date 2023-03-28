<?php


namespace Module\Member\Model;


use Illuminate\Database\Eloquent\Model;
use ModStart\Admin\Model\Data;

class MemberUpload extends Model
{
    protected $table = 'member_upload';

    public function data()
    {
        return $this->hasOne(Data::class, 'id', 'dataId');
    }
}
