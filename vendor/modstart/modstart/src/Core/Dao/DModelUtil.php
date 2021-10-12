<?php


namespace ModStart\Core\Dao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DModelUtil
{
    /**
     * @param $model
     * @return Builder | Model
     */
    public static function model($conn, $model)
    {
        $m = new DynamicModel();
        $m->setTable($model);
        $m->setConnection($conn);
        return $m;
    }

    public static function get($conn, $model, $where, $fields = ['*'])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $m = self::model($conn, $model)->where($where)->first($fields);
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function insert($conn, $model, $data)
    {
        $m = self::model($conn, $model);
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        return $m->toArray();
    }

    public static function insertAll($conn, $model, $datas, $updateTimestamp = true)
    {
        if ($updateTimestamp) {
            foreach ($datas as $i => $data) {
                $datas[$i]['created_at'] = date('Y-m-d H:i:s');
                $datas[$i]['updated_at'] = date('Y-m-d H:i:s');
            }
        }
        DB::connection($conn)->table($model)->insert($datas);
    }

    public static function delete($conn, $model, $where)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        return self::model($conn, $model)->where($where)->delete();
    }

    public static function update($conn, $model, $where, $data)
    {
        if (empty($where)) {
            return null;
        }
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        if (empty($data)) {
            return null;
        }
        return self::model($conn, $model)->where($where)->update($data);
    }
}