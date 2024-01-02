<?php


namespace Module\Cms\Model;


use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    protected $table = 'cms_content';

    public function cat()
    {
        return $this->hasOne(CmsCat::class, 'id', 'catId');
    }
}
