<?php

namespace Module\Vendor\Recycle;

use ModStart\Core\Dao\TenantModelUtil;

class TenantRecycleUtil
{
    public static function tableAdd($tenant, $table, $tableId, $data)
    {
        TenantModelUtil::insert($tenant, 'tenant_recycle_table', [
            'table' => $table,
            'tableId' => $tableId,
            'content' => json_encode($data),
        ]);
    }
}
