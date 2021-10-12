<?php

namespace ModStart\Core\Dao;


class TenantType
{
    // table name : table
    const BARE = 1;
    // table name : t_<tenant>_table
    const PREFIXED = 2;
}