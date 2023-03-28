<?php


namespace Module\Member\Model;


use Illuminate\Database\Eloquent\Model;

class MemberDataStatistic extends Model
{
    protected $table = 'member_data_statistic';

    public static function getCreateMemberUser($id)
    {
        $first = self::where('id', $id)->first();
        if (empty($first)) {
            $m = new self();
            $m->id = $id;
            $m->sizeLimit = modstart_config('Member_DataStatisticDefaultLimit', 1024);
            $m->save();
            self::updateMemberUserUsedSize($id);
            return self::getCreateMemberUser($id);
        }
        return $first->toArray();
    }

    public static function updateMemberUser($id, $data)
    {
        $m = self::where('id', $id)->first();
        $updateSize = false;
        if (empty($m)) {
            $m = new self();
            $m->id = $id;
            $updateSize = true;
        }
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        if ($updateSize) {
            self::updateMemberUserUsedSize($id);
        }
    }

    public static function calcMemberUserUsedSize($id)
    {
        $total = MemberUpload::where(['userId' => $id])
            ->leftJoin('data', 'data.id', '=', 'member_upload.dataId')
            ->sum('data.size');
        return intval($total);
    }

    public static function updateMemberUserUsedSize($id)
    {
        $update = [
            'sizeUsed' => self::calcMemberUserUsedSize($id),
        ];
        self::where('id', $id)->update($update);
    }
}
