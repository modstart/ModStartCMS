<?php

namespace ModStart\Core\Dao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ModStart\Core\Util\MemCacheUtil;

class TenantModelUtil
{
    private static function tenant($tenant)
    {
        return MemCacheUtil::remember("TT:$tenant", function () use ($tenant) {
            return Cache::remember("TT:$tenant", 60, function () use ($tenant) {
                return ModelUtil::get('tenant', ['tenant' => $tenant], ['connectionId', 'type']);
            });
        });
    }

    private static function connection($tenant)
    {
        $connection = MemCacheUtil::remember("TC:$tenant", function () use ($tenant) {
            return Cache::remember("TC:$tenant", 60, function () use ($tenant) {
                $t = self::tenant($tenant);
                if (empty($t)) {
                    return null;
                }
                return ModelUtil::get('tenant_connection', ['id' => $t['connectionId']], ['host', 'database', 'username', 'password']);
            });
        });
        if (empty($connection)) {
            return 'mysql';
        }
        Config::set('database.connections.mysql_tenant_' . $tenant, [
            'driver' => 'mysql',
            'host' => $connection['host'],
            'database' => $connection['database'],
            'username' => $connection['username'],
            'password' => $connection['password'],
            'charset' => config('database.connections.mysql.charset'),
            'collation' => config('database.connections.mysql.collation'),
            'prefix' => config('database.connections.mysql.prefix'),
            'strict' => config('database.connections.mysql.strict'),
        ]);
        return 'mysql_tenant_' . $tenant;
    }

    public static function table($tenant, $table)
    {
        $t = self::tenant($tenant);
        if ($t && $t['type'] === TenantType::PREFIXED) {
            return "t_${tenant}_${table}";
        }
        return $table;
    }

    /**
     * @param $tenant
     * @return \Illuminate\Database\Connection
     */
    public static function DBconnection($tenant)
    {
        return DB::connection(self::connection($tenant));
    }

    public static function DBStatement($tenant, $sql)
    {
        self::DBconnection($tenant)->statement($sql);
    }

    public static function ddlHasTable($tenant, $table)
    {
        return self::DBconnection($tenant)->getSchemaBuilder()->hasTable(self::table($tenant, $table));
    }

    public static function ddlFieldTypeIsCorrect($fieldType)
    {
        if (preg_match('/^INT$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^DECIMAL\\(\\d+,\\d+\\)$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^DATE$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^DATETIME$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^TIME/', $fieldType)) {
            return true;
        }
        if (preg_match('/^VARCHAR\\(\\d+\\)$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^TEXT$/', $fieldType)) {
            return true;
        }
        return false;
    }

    public static function ddlFieldAdd($tenant, $table, $fieldName, $fieldType)
    {
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        if (!self::ddlFieldTypeIsCorrect($fieldType)) {
            throw new \Exception('DDL fieldType error : ' . $fieldType);
        }
        $table = self::table($tenant, $table);
        $sql = "ALTER TABLE `$table` ADD `$fieldName` $fieldType DEFAULT NULL";
        self::DBStatement($tenant, $sql);
    }

    public static function ddlFieldChange($tenant, $table, $fieldNameOld, $fieldName, $fieldType)
    {
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $fieldNameOld)) {
            throw new \Exception('DDL fieldNameOld error');
        }
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        if (!self::ddlFieldTypeIsCorrect($fieldType)) {
            throw new \Exception('DDL fieldType error : ' . $fieldType);
        }
        $table = self::table($tenant, $table);
        $sql = "ALTER TABLE `$table` CHANGE `$fieldNameOld` `$fieldName` $fieldType DEFAULT NULL";
        self::DBStatement($tenant, $sql);
    }

    public static function ddlFieldModify($tenant, $table, $fieldName, $fieldType)
    {
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        if (!self::ddlFieldTypeIsCorrect($fieldType)) {
            throw new \Exception('DDL fieldType error : ' . $fieldType);
        }
        $table = self::table($tenant, $table);
        $sql = "ALTER TABLE `$table` MODIFY `$fieldName` $fieldType DEFAULT NULL";
        self::DBStatement($tenant, $sql);
    }

    public static function ddlFieldDrop($tenant, $table, $fieldName)
    {
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-z][a-zA-Z0-9_]+$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        $table = self::table($tenant, $table);
        $sql = "ALTER TABLE `$table` DROP `$fieldName`";
        self::DBStatement($tenant, $sql);
    }

    public static function connectionAutoAssign($tenant, $type = TenantType::PREFIXED)
    {
        $t = ModelUtil::get('tenant', ['tenant' => $tenant], ['connectionId', 'type']);
        if (!empty($t)) {
            return true;
        }
        $connection = ModelUtil::model('tenant_connection')
            ->where('available', '>', 0)
            ->orderBy('priority', 'asc')
            ->first();
        if (empty($connection)) {
            ModelUtil::insert('tenant', ['tenant' => $tenant, 'connectionId' => 0, 'type' => $type]);
            return true;
        }
        ModelUtil::insert('tenant', ['tenant' => $tenant, 'connectionId' => $connection->id, 'type' => $type,]);
        ModelUtil::update('tenant_connection', $connection->id, [
            'used' => ModelUtil::count('tenant', ['connectionId' => $connection->id]),
            'available' => $connection->available - 1,
        ]);
        Cache::forget("TT:$tenant");
        Cache::forget("TC:$tenant");
        return true;
    }

    /**
     * @param $tenants
     * @param $table
     * @param $schemaCallback
     * @example
     *
     * TenantModelUtil::migrates(
     *     [1, 2, 3, 4, 5],
     *     'table1',
     *     function ($table, $schema) {
     *         $schema->create($table, function (Blueprint $table) {
     *
     *             $table->increments('id');
     *             $table->timestamps();
     *
     *             $table->string('foo', 100)->nullable()->comment('');
     *             $table->string('bar', 100)->nullable()->comment('');
     *
     *         });
     *     }
     * );
     */
    public static function migrates($tenants, $table, $schemaCallback)
    {
        if (empty($tenants)) {
            return;
        }
        if (!is_array($tenants)) {
            $tenants = [$tenants];
        }
        foreach ($tenants as $tenant) {
            $tenantTable = self::table($tenant, $table);
            $tenantConnection = self::connection($tenant);
            $schemaCallback($tenantTable, Schema::connection($tenantConnection));
        }
    }

    /**
     * @param $model
     * @return Builder | Model
     */
    public static function model($tenant, $model)
    {
        $m = new DynamicModel();
        $m->setTable(self::table($tenant, $model));
        $m->setConnection(self::connection($tenant));
        return $m;
    }

    /**
     * @param $model
     * @return Builder | Model
     */
    public static function modelTenant($tenant, $modelWithTenant)
    {
        $m = new DynamicModel();
        $m->setTable($modelWithTenant);
        $m->setConnection(self::connection($tenant));
        return $m;
    }

    public static function all($tenant, $model, $where = [], $fields = ['*'], $order = null)
    {
        if ($order) {
            return self::model($tenant, $model)->where($where)->orderBy($order[0], $order[1])->get($fields)->toArray();
        }
        return self::model($tenant, $model)->where($where)->get($fields)->toArray();
    }

    public static function allIn($tenant, $model, $field, $in, $fields = ['*'])
    {
        return self::model($tenant, $model)->whereIn($field, $in)->get($fields)->toArray();
    }

    public static function get($tenant, $model, $where, $fields = ['*'])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $m = self::model($tenant, $model)->where($where)->first($fields);
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function exists($tenant, $model, $where)
    {
        return null != self::get($tenant, $model, $where);
    }

    public static function insert($tenant, $model, $data)
    {
        $m = self::model($tenant, $model);
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        return $m->toArray();
    }

    public static function delete($tenant, $model, $where)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        return self::model($tenant, $model)->where($where)->delete();
    }

    public static function update($tenant, $model, $where, $data)
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
        return self::model($tenant, $model)->where($where)->update($data);
    }

    public static function count($tenant, $model, $where = [])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        return self::model($tenant, $model)->where($where)->count();
    }

    public static function paginate($tenant, $model, $page, $pageSize, $option = [])
    {
        $m = self::model($tenant, $model);
        $modelName = self::table($tenant, $model);

        if (!empty($option['joins'])) {
            $select = [];
            if (empty($option['fields'])) {
                $select[] = $modelName . '.*';
            }
            foreach ($option['joins'] as $join) {
                if (!empty($join['table'])) {
                    $m = $m->leftJoin($join['table'][0], $join['table'][1], $join['table'][2], $join['table'][3]);
                    if (!empty($join['fields'])) {
                        foreach ($join['fields'] as $fieldAlias => $fieldTable) {
                            array_push($select, "$fieldTable as $fieldAlias");
                        }
                    }
                }
            }
            if (!empty($select)) {
                $m = call_user_func_array(array($m, 'select'), $select);
            }
        }

        ModelUtil::paginateMergeConditionParam($m, $option);

        if (!empty($option['order'])) {
            if (is_array($option['order'][0])) {
                foreach ($option['order'] as &$order) {
                    $m = $m->orderBy($order[0], $order[1]);
                }
            } else {
                $m = $m->orderBy($option['order'][0], $option['order'][1]);
            }
        }

        if (!empty($option['fields'])) {
            $m = $m->select($option['fields']);
        }

        $m = $m->paginate($pageSize, ['*'], 'page', $page)->toArray();

        return [
            'total' => $m['total'],
            'records' => $m['data']
        ];
    }

    public static function join($tenant, &$data, $dataModelKey = 'userId', $dataMergedKey = '_user', $model = 'join_model', $modelPrimaryKey = 'id')
    {
        if (empty($data)) {
            return;
        }

        $ids = array_map(function ($item) use ($dataModelKey) {
            return $item[$dataModelKey];
        }, $data);

        $joinData = self::model($tenant, $model)->whereIn($modelPrimaryKey, $ids)->get()->toArray();

        $joinDataMap = array_build($joinData, function ($k, $v) use ($modelPrimaryKey) {
            return [$v[$modelPrimaryKey], $v];
        });
        foreach ($data as &$item) {
            $key = $item[$dataModelKey];
            if (isset($joinDataMap[$key])) {
                $item[$dataMergedKey] = $joinDataMap[$key];
            } else {
                $item[$dataMergedKey] = null;
            }
        }
    }


}
