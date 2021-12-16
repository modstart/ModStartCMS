<?php


namespace App\Web\Controller;

use Illuminate\Support\Facades\Input;
use ModStart\Core\Input\Response;
use Module\Cms\Provider\CmsHomePageProvider;
use Module\Vendor\Installer\Util\InstallerUtil;
use Module\Vendor\Provider\HomePage\HomePageProvider;

class IndexController extends BaseController
{
    public function index()
    {
        InstallerUtil::checkForInstallRedirect();
        return HomePageProvider::call(__METHOD__, CmsHomePageProvider::ACTION);
    }
}
