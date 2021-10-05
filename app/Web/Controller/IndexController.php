<?php


namespace App\Web\Controller;

use Module\Vendor\Installer\Util\InstallerUtil;

class IndexController extends BaseController
{
    public function index(\Module\Cms\Web\Controller\IndexController $cms)
    {
        InstallerUtil::checkForInstallRedirect();
        return $cms->index();
    }
}
