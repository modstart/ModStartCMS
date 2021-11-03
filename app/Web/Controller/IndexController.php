<?php


namespace App\Web\Controller;

use Module\CmsWriter\Provider\CmsWriterHomePageProvider;
use Module\Vendor\Installer\Util\InstallerUtil;
use Module\Vendor\Provider\HomePage\HomePageProvider;

class IndexController extends BaseController
{
    public function index()
    {
        InstallerUtil::checkForInstallRedirect();
        return HomePageProvider::call(__METHOD__, CmsWriterHomePageProvider::ACTION);
    }
}
