<?php


namespace Module\Vendor\Installer\Util;


class InstallerUtil
{
    public static function checkForInstallRedirect()
    {
        $envPath = base_path('.env');
        $installLockPath = storage_path('install.lock');
        if (!file_exists($installLockPath)) {
            header('Location: ' . modstart_web_url('install.php'));
            exit();
        }
    }
}
