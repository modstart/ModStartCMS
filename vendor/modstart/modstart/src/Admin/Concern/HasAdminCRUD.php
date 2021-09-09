<?php


namespace ModStart\Admin\Concern;

use ModStart\Support\Concern\HasPageTitleInfo;

trait HasAdminCRUD
{
    use HasPageTitleInfo;
    use HasAdminGrid;
    use HasAdminDetail;
    use HasAdminForm;
}
