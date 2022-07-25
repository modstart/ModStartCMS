<?php


namespace ModStart\Admin\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use ModStart\Admin\Event\AdminUserLoginAttemptEvent;
use ModStart\Admin\Event\AdminUserLoginFailedEvent;
use ModStart\Admin\Type\AdminLogType;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\AgentUtil;

class Admin
{
    const ADMIN_USER_ID_SESSION_KEY = '_adminUserId';

    public static function isLogin()
    {
        return self::id() > 0;
    }

    public static function isNotLogin()
    {
        return !self::isLogin();
    }

    public static function id()
    {
        return intval(Session::get(self::ADMIN_USER_ID_SESSION_KEY, null));
    }

    public static function get($adminUserId)
    {
        return ModelUtil::get('admin_user', ['id' => $adminUserId]);
    }

    public static function getByUsername($username)
    {
        return ModelUtil::get('admin_user', ['username' => $username]);
    }

    public static function passwordEncrypt($password, $passwordSalt)
    {
        return md5(md5($password) . md5($passwordSalt));
    }

    public static function add($username, $password, $ignorePassword = false)
    {
        $passwordSalt = Str::random(16);
        $data = [];
        $data['username'] = $username;
        if (!$ignorePassword) {
            $data['passwordSalt'] = $passwordSalt;
            $data['password'] = self::passwordEncrypt($password, $passwordSalt);
        }
        return ModelUtil::insert('admin_user', $data);
    }

    public static function loginByPhone($phone)
    {
        $adminUser = ModelUtil::get('admin_user', ['phone' => $phone]);
        if (empty($adminUser)) {
            AdminUserLoginFailedEvent::fire(0, null, Request::ip(), AgentUtil::getUserAgent());
            return Response::generate(-1, L('User Not Exists'));
        }
        AdminUserLoginAttemptEvent::fire($adminUser['id'], Request::ip(), AgentUtil::getUserAgent());
        ModelUtil::update('admin_user', $adminUser['id'], [
            'lastLoginIp' => Request::ip(),
            'lastLoginTime' => Carbon::now(),
        ]);
        return Response::generateSuccessData($adminUser);
    }

    public static function login($username, $password)
    {
        $adminUser = ModelUtil::get('admin_user', ['username' => $username]);
        if (empty($adminUser)) {
            AdminUserLoginFailedEvent::fire(0, $username, Request::ip(), AgentUtil::getUserAgent());
            return Response::generate(-1, L('User Not Exists'));
        }
        AdminUserLoginAttemptEvent::fire($adminUser['id'], Request::ip(), AgentUtil::getUserAgent());
        if ($adminUser['password'] != self::passwordEncrypt($password, $adminUser['passwordSalt'])) {
            AdminUserLoginFailedEvent::fire($adminUser['id'], $username, Request::ip(), AgentUtil::getUserAgent());
            return Response::generate(-2, L('Password Incorrect'));
        }
        ModelUtil::update('admin_user', $adminUser['id'], [
            'lastLoginIp' => Request::ip(),
            'lastLoginTime' => Carbon::now(),
        ]);
        return Response::generateSuccessData($adminUser);
    }

    public static function ruleChanged($adminUserId, $ruleChanged)
    {
        ModelUtil::update('admin_user', ['id' => $adminUserId], ['ruleChanged' => boolval($ruleChanged)]);
    }

    public static function listRolesByUserId($adminUserId)
    {
        $adminUser = ModelUtil::get('admin_user', $adminUserId);
        if (empty($adminUser)) {
            return Response::generate(-1, L('User Not Exists'));
        }
        $roles = ModelUtil::all('admin_user_role', ['userId' => $adminUserId], ['roleId']);
        ModelUtil::join($roles, 'roleId', 'role', 'admin_role', 'id');
        foreach ($roles as $k => $role) {
            $roles[$k]['name'] = $role['role']['name'];
        }
        ModelUtil::joinAll($roles, 'roleId', 'rules', 'admin_role_rule', 'roleId');
        return Response::generate(0, null, $roles);
    }

    public static function changePassword($id, $old, $new, $ignoreOld = false)
    {
        $adminUser = ModelUtil::get('admin_user', ['id' => $id]);
        if (empty($adminUser)) {
            return Response::generate(-1, L('Admin user not exists'));
        }
        if ($adminUser['password'] != self::passwordEncrypt($old, $adminUser['passwordSalt'])) {
            if (!$ignoreOld) {
                return Response::generate(-1, L('Old Password Incorrect'));
            }
        }
        $passwordSalt = Str::random(16);
        $data = [];
        $data['password'] = self::passwordEncrypt($new, $passwordSalt);
        $data['passwordSalt'] = $passwordSalt;
        $data['lastChangePwdTime'] = Carbon::now();
        ModelUtil::update('admin_user', ['id' => $adminUser['id']], $data);
        return Response::generate(0, 'ok');
    }

    public static function addInfoLog($adminUserId, $summary, $content = [])
    {
        static $exists = null;
        if (null === $exists) {
            $exists = Schema::hasTable('admin_log');
        }
        if (!$exists) {
            return;
        }
        $adminLog = ModelUtil::insert('admin_log', ['adminUserId' => $adminUserId, 'type' => AdminLogType::INFO, 'summary' => $summary]);
        if (!empty($content)) {
            ModelUtil::insert('admin_log_data', ['id' => $adminLog['id'], 'content' => json_encode($content)]);
        }
    }

    public static function addErrorLog($adminUserId, $summary, $content = [])
    {
        static $exists = null;
        if (null === $exists) {
            $exists = Schema::hasTable('admin_log');
        }
        if (!$exists) {
            return;
        }
        $adminLog = ModelUtil::insert('admin_log', ['adminUserId' => $adminUserId, 'type' => AdminLogType::ERROR, 'summary' => $summary]);
        if (!empty($content)) {
            ModelUtil::insert('admin_log_data', ['id' => $adminLog['id'], 'content' => json_encode($content)]);
        }
    }

    public static function addInfoLogIfChanged($adminUserId, $summary, $old, $new)
    {
        $changed = [];
        if (empty($old) && empty($new)) {
            return;
        }
        foreach ($old as $k => $oldValue) {
            if (!array_key_exists($k, $new)) {
                $changed['Delete:' . $k . ':Old'] = $oldValue;
                continue;
            }
            if ($new[$k] != $oldValue) {
                $changed['Change:' . $k . ':Old'] = $oldValue;
                continue;
            }
        }
        foreach ($new as $k => $newValue) {
            if (!array_key_exists($k, $old)) {
                $changed['Add:' . $k . ':New'] = $newValue;
                continue;
            }
        }
        if (empty($changed)) {
            return;
        }
        self::addInfoLog($adminUserId, $summary, $changed);
    }


}
