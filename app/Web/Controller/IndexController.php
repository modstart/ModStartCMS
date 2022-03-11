<?php


namespace App\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Core\Util\StrUtil;
use Module\Vendor\Installer\Util\InstallerUtil;
use Module\Vendor\Provider\HomePage\HomePageProvider;

class IndexController extends BaseController
{
    public function index()
    {
        InstallerUtil::checkForInstallRedirect();
        return HomePageProvider::call(__METHOD__, '\\Module\\Cms\\Web\\Controller\\IndexController@index');
    }

    public function testx()
    {
    }
}
