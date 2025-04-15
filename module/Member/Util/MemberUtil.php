<?php

namespace Module\Member\Util;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\AgentUtil;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\EncodeUtil;
use ModStart\Core\Util\FormatUtil;
use ModStart\Core\Util\LockUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Data\DataManager;
use ModStart\Data\Event\DataFileUploadedEvent;
use Module\Member\Events\MemberUserDeletedEvent;
use Module\Member\Events\MemberUserLoginAttemptEvent;
use Module\Member\Events\MemberUserLoginFailedEvent;
use Module\Member\Model\MemberUser;
use Module\Member\Type\MemberMessageStatus;
use Module\Member\Type\MemberPasswordStrength;
use Module\Member\Type\MemberStatus;
use Module\Vendor\Type\DeviceType;
use Module\Vendor\Util\CacheUtil;

class MemberUtil
{
    public static function clearCache($memberUserId)
    {
        CacheUtil::forget('MemberUser:' . $memberUserId);
    }

    /**
     * @return mixed
     * @since 1.5.0
     */
    public static function total()
    {
        return Cache::remember('MemberUserTotal', 60, function () {
            return ModelUtil::count('member_user');
        });
    }

    public static function get($id)
    {
        return ModelUtil::get(MemberUser::class, ['id' => $id]);
    }

    public static function getCached($id)
    {
        return CacheUtil::remember('MemberUser:' . $id, 3600, function () use ($id) {
            return self::get($id);
        });
    }

    /**
     * @param $memberUser
     * @since Member 1.6.0
     */
    public static function processDefault(&$memberUser)
    {
        if (empty($memberUser)) {
            return;
        }
        if (empty($memberUser['nickname'])) {
            // 这里暂时不设置，否则会出现前端无法判别是否有设置昵称的问题
            // $memberUser['nickname'] = $memberUser['username'];
        }
        if (empty($memberUser['avatar'])) {
            $memberUser['avatar'] = AssetsUtil::fixFull('asset/image/avatar.svg', false);
        }
        if (empty($memberUser['avatarMedium'])) {
            $memberUser['avatarMedium'] = AssetsUtil::fixFull('asset/image/avatar.svg', false);
        }
        if (empty($memberUser['avatarBig'])) {
            $memberUser['avatarBig'] = AssetsUtil::fixFull('asset/image/avatar.svg', false);
        }
    }

    private static function processBasicFields($keepFields)
    {
        $keepFieldsBasic = [
            'id', 'username', 'avatar', 'created_at', 'signature', 'nickname',
        ];
        if (null === $keepFields) {
            $keepFields = $keepFieldsBasic;
        } else {
            $keepFieldsProcess = [];
            foreach ($keepFields as $k) {
                if ('<basic>' == $k) {
                    $keepFieldsProcess = array_merge($keepFieldsProcess, $keepFieldsBasic);
                } else {
                    $keepFieldsProcess[] = $k;
                }
            }
            $keepFields = $keepFieldsProcess;
        }
        return $keepFields;
    }

    public static function fixAvatar($avatar)
    {
        return AssetsUtil::fixFullOrDefault($avatar, 'asset/image/avatar.svg');
    }

    public static function getBasic($id, $keepFields = null)
    {
        $keepFields = self::processBasicFields($keepFields);
        $item = self::get($id);
        if (empty($item)) {
            return null;
        }
        if (empty($item['nickname'])) {
            $item['nickname'] = $item['username'];
        }
        $item['avatar'] = self::fixAvatar($item['avatar']);
        $result = [];
        foreach ($keepFields as $keepField) {
            if (isset($item[$keepField])) {
                $result[$keepField] = $item[$keepField];
            } else {
                $result[$keepField] = null;
            }
        }
        return $result;
    }

    public static function listViewName($ids)
    {
        $results = [];
        $memberUsers = ModelUtil::allIn('member_user', 'id', $ids);
        foreach ($memberUsers as $memberUser) {
            $results[] = self::viewName($memberUser);
        }
        return $results;
    }

    public static function listUsers($ids)
    {
        return ModelUtil::allIn('member_user', 'id', $ids);
    }

    public static function convertOneToBasic($memberUser)
    {
        return [
            'id' => $memberUser ? $memberUser['id'] : 0,
            'username' => $memberUser ? $memberUser['username'] : null,
            'nickname' => $memberUser ? (empty($memberUser['nickname']) ? $memberUser['username'] : $memberUser['nickname']) : null,
            'created_at' => $memberUser ? $memberUser['created_at'] : null,
            'signature' => isset($memberUser['signature']) ? $memberUser['signature'] : null,
            'avatar' => AssetsUtil::fixFullOrDefault($memberUser ? $memberUser['avatar'] : null, 'asset/image/avatar.svg'),
        ];
    }

    public static function convertToBasic($memberUsers)
    {
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'username' => $item['username'],
                'nickname' => empty($item['nickname']) ? $item['username'] : $item['nickname'],
                'created_at' => $item['created_at'],
                'signature' => isset($item['signature']) ? $item['signature'] : null,
                'avatar' => AssetsUtil::fixFullOrDefault($item['avatar'], 'asset/image/avatar.svg'),
            ];
        }, $memberUsers);
    }

    public static function listUsersBasic($ids)
    {
        return self::convertToBasic(self::listUsers($ids));
    }

    /**
     * @param $id
     * @return mixed|string
     * @deprecated delete at 2024-07-09
     */
    public static function getViewName($id)
    {
        return self::viewName(self::get($id));
    }

    public static function viewNameById($id)
    {
        return self::viewName(self::get($id));
    }

    public static function viewName($memberUser)
    {
        if ($memberUser && is_numeric($memberUser)) {
            return self::viewNameById($memberUser);
        }
        if (empty($memberUser)) {
            return '-';
        }
        if (!empty($memberUser['nickname'])) {
            return $memberUser['nickname'];
        }
        if (!empty($memberUser['username'])) {
            return $memberUser['username'];
        }
        return "ID-$memberUser[id]";
    }

    public static function update($id, $data)
    {
        return ModelUtil::update('member_user', ['id' => $id], $data);
    }

    /**
     * 更新基本数据，同时检查唯一性
     *
     * @param $id
     * @param $data
     * @return array
     */
    public static function updateBasicWithUniqueCheck($id, $data)
    {
        if (empty($data)) {
            return Response::generate(0, 'ok');
        }
        foreach (['username' => '用户名', 'phone' => '手机', 'email' => '邮箱',] as $field => $fieldTitle) {
            if (isset($data[$field])) {
                if (empty($data[$field])) {
                    $data[$field] = null;
                    continue;
                }
                $exists = ModelUtil::all('member_user', [$field => $data[$field]]);
                if (count($exists) > 1) {
                    return Response::generate(-1, $fieldTitle . '重复');
                }
                if (count($exists) == 1) {
                    if ($exists[0]['id'] != $id) {
                        return Response::generate(-1, $fieldTitle . '重复');
                    }
                }
            }
        }
        self::update($id, $data);
        return Response::generate(0, 'ok');
    }

    /**
     * 登录，email phone username 只能选择一个作为登录凭证
     *
     * @param string $username
     * @param string $phone
     * @param string $email
     * @param string $password
     * @return array ['code'=>'0','msg'=>'ok','data'=>MemberUser]
     */
    public static function login($username = '', $phone = '', $email = '', $password = '')
    {
        $email = trim($email);
        $phone = trim($phone);
        $username = trim($username);

        if (!($email || $phone || $username)) {
            return Response::generate(-1, '所有登录字段均为空');
        }
        if (!$password) {
            return Response::generate(-2, '密码为空');
        }

        if ($email) {
            if (!FormatUtil::isEmail($email)) {
                return Response::generate(-3, '邮箱格式不正确');
            }
            $where = [
                'email' => $email
            ];
        } else if ($phone) {
            if (!preg_match('/(^1[0-9]{10}$)/', $phone)) {
                return Response::generate(-4, '手机格式不正确');
            }
            $where = [
                'phone' => $phone
            ];
        } else if ($username) {
            if (strpos($username, '@') !== false) {
                return Response::generate(-5, '用户名格式不正确');
            }
            $where = [
                'username' => $username
            ];
        }

        $memberUser = ModelUtil::get('member_user', $where);
        if (empty($memberUser)) {
            return Response::generate(-6, '登录失败:用户名或密码错误');
        }

        MemberUserLoginAttemptEvent::fire($memberUser['id'], Request::ip(), AgentUtil::getUserAgent());

        if ($memberUser['password'] != EncodeUtil::md5WithSalt($password, $memberUser['passwordSalt'])) {
            MemberUserLoginFailedEvent::fire($memberUser['id'], $memberUser['username'], Request::ip(), AgentUtil::getUserAgent());
            return Response::generate(-7, '登录失败:用户名或密码错误');
        }

        switch ($memberUser['status']) {
            case MemberStatus::FORBIDDEN:
                return Response::generateError(-8, '登录失败:当前用户已被禁用');
        }
        return Response::generateSuccessData($memberUser);
    }

    public static function autoSetUsernameNickname($memberUserId, $suggestName, $randomLength = 6)
    {
        if (preg_match('/\\{.*\\}/', $suggestName)) {
            $memberUser = self::get($memberUserId);
            $map = [
                '{Phone}' => $memberUser['phone'],
                '{Phone4}' => substr($memberUser['phone'], 7),
                '{Uid}' => $memberUser['id'],
            ];
            $suggestName = str_replace(array_keys($map), array_values($map), $suggestName);
            $randomLength = 0;
        }
        self::suggestUsernameNickname($memberUserId, $suggestName, $randomLength);
    }

    public static function getSuggestUsernameNickname($suggest)
    {
        $suggestName = $suggest . Str::random(1);
        for ($i = 0; $i < 20; $i++) {
            $found = ModelUtil::model('member_user')
                ->where(['username' => $suggestName])
                ->orWhere(['nickname' => $suggestName])
                ->first();
            if (empty($found)) {
                return $suggestName;
            }
            $suggestName = $suggestName . Str::random(1);
        }
        return $suggestName . Str::random(10);
    }

    private static function suggestUsernameNickname($memberUserId, $prefix = '用户', $randomLength = 6)
    {
        if ($randomLength > 0) {
            $suggestName = $prefix . RandomUtil::string($randomLength);
        } else {
            $suggestName = $prefix;
        }
        for ($i = 0; $i < 20; $i++) {
            $found = ModelUtil::model('member_user')
                ->where('id', '<>', $memberUserId)
                ->where(function ($query) use ($suggestName) {
                    $query->where('username', $suggestName)
                        ->orWhere('nickname', $suggestName);
                })
                ->first();
            if (empty($found)) {
                break;
            }
            $suggestName = $suggestName . Str::random(1);
        }
        ModelUtil::update('member_user', $memberUserId, [
            'username' => $suggestName,
            'nickname' => $suggestName,
        ]);
    }

    public static function registerId($id, $data = [])
    {
        $memberUser = ModelUtil::insert('member_user', array_merge([
            'id' => $id,
            'status' => MemberStatus::NORMAL,
            'vipId' => MemberVipUtil::defaultVipId(),
            'groupId' => MemberGroupUtil::defaultGroupId(),
            'isDeleted' => false,
        ], $data));
        return Response::generate(0, 'ok', $memberUser);
    }

    public static function registerUsername($username)
    {
        return self::register($username, '', '', '', true);
    }

    public static function registerUsernameQuick($username)
    {
        $suggestionUsername = $username;
        if ($suggestionUsername == modstart_config('Member_RegisterUsernameSuggest', '用户')) {
            $suggestionUsername = $suggestionUsername . Str::random(6);
        }
        for ($i = 1; $i < 10; $i++) {
            $ret = self::register($suggestionUsername, '', '', '', true);
            if ($ret['code']) {
                $suggestionUsername = $suggestionUsername . Str::random(1);
            } else {
                return $ret;
            }
        }
        return Response::generateError('注册失败');
    }

    /**
     * 用户注册
     * email phone username param.nickname 可以只选择一个为注册ID
     * username 和 nickname 的区别是一个可以作为登录名，一个不可以，原则上公开界面上优先显示 nickname，理论上 nickname 可以重复
     *
     * @param $username string 用户名
     * @param $phone string 手机号
     * @param $email string 邮箱
     * @param $password string 密码
     * @param $ignorePassword bool 是否忽略密码
     * @param $param array 参数
     * @return array ['code'=>'0','msg'=>'ok','data'=>'member_user array']
     */
    public static function register($username = '', $phone = '', $email = '', $password = '', $ignorePassword = false, $param = [])
    {
        $param = array_merge([
            'nickname' => $username,
        ], $param);
        if (empty($param['nickname'])) {
            $param['nickname'] = null;
        }

        $email = trim($email);
        $phone = trim($phone);
        $username = trim($username);

        if (!($email || $phone || $username || $param['nickname'])) {
            return Response::generate(-1, '所有注册字段均为空');
        }

        if ($email) {
            $ret = self::uniqueCheck('email', $email);
            if ($ret['code']) {
                return $ret;
            }
        } else {
            $email = null;
        }
        if ($phone) {
            $ret = self::uniqueCheck('phone', $phone);
            if ($ret['code']) {
                return $ret;
            }
        } else {
            $phone = null;
        }
        if ($username) {
            $ret = self::uniqueCheck('username', $username);
            if ($ret['code']) {
                return $ret;
            }
            $minLength = modstart_config('Member_UsernameMinLength', 3);
            if (strlen($username) < $minLength) {
                return Response::generate(-1, '用户名至少' . $minLength . '个字符');
            }
            // 为了统一登录时区分邮箱
            if (Str::contains($username, '@')) {
                return Response::generate(-1, '用户名不能包含特殊字符');
            }
            // 为了统一登录时候区分手机号
            if (preg_match('/^[0-9]{11}$/', $username)) {
                return Response::generate(-1, '用户名不能为纯数字');
            }
        } else {
            $username = null;
        }
        if (!$ignorePassword) {
            if (empty($password)) {
                return Response::generate(-3, '密码不合法');
            }
            $ret = self::passwordStrengthCheck($password);
            if (Response::isError($ret)) {
                return Response::generate(-1, $ret['msg']);
            }
        }
        $passwordSalt = Str::random(16);
        $memberUser = ModelUtil::insert('member_user', [
            'status' => MemberStatus::NORMAL,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'nickname' => $param['nickname'],
            'password' => $ignorePassword ? null : EncodeUtil::md5WithSalt($password, $passwordSalt),
            'passwordSalt' => $ignorePassword ? null : $passwordSalt,
            'vipId' => MemberVipUtil::defaultVipId(),
            'groupId' => MemberGroupUtil::defaultGroupId(),
            'isDeleted' => false,
        ]);
        return Response::generate(0, 'ok', $memberUser);
    }

    /**
     * 唯一性检查
     *
     * @param string $type = email | phone | username
     * @param $value
     * @param int $ignoreUserId
     * @return array ['code'=>'0','msg'=>'ok']
     */
    public static function uniqueCheck($type, $value, $ignoreUserId = 0)
    {
        $value = trim($value);
        switch ($type) {
            case 'email' :
                if (!FormatUtil::isEmail($value)) {
                    return Response::generate(-1, '邮箱格式不正确');
                }
                break;
            case 'phone' :
                if (!preg_match('/(^1[0-9]{10}$)/', $value)) {
                    return Response::generate(-1, '手机格式不正确');
                }
                break;
            case 'username' :
                if (strpos($value, '@') !== false) {
                    return Response::generate(-1, '用户名格式不正确');
                }
                break;
            case 'nickname':
                break;
            default :
                return Response::generate(-1, '未能识别的类型' . $type);
        }

        $memberUser = ModelUtil::get('member_user', [$type => $value]);
        if (empty ($memberUser)) {
            return Response::generate(0, 'ok');
        }

        $lang = [
            'nickname' => '昵称',
            'username' => '用户名',
            'email' => '邮箱',
            'phone' => '手机号'
        ];
        if ($ignoreUserId == $memberUser['id']) {
            return Response::generate(0, 'ok');
        }
        return Response::generate(-2, $lang [$type] . '已经被占用');
    }


    public static function getByUsername($username)
    {
        return ModelUtil::get('member_user', ['username' => $username]);
    }

    public static function getByEmail($email)
    {
        return ModelUtil::get('member_user', ['email' => $email]);
    }

    public static function getByPhone($phone)
    {
        return ModelUtil::get('member_user', ['phone' => $phone]);
    }

    public static function changeNickname($memberUserId, $nickname)
    {
        $ret = self::uniqueCheck('nickname', $nickname, $memberUserId);
        if (Response::isError($ret)) {
            return $ret;
        }
        ModelUtil::update('member_user', $memberUserId, ['nickname' => $nickname]);
        return Response::generate(0, 'ok');
    }

    /**
     * 修改密码
     * 注意参数顺序!!!
     *
     * @param $memberUserId int 用户id
     * @param $newPassword string 新密码
     * @param $oldPassword string 旧密码
     * @param $ignoreOldPassword bool 是否忽略旧密码
     * @return array
     */
    public static function changePassword($memberUserId, $newPassword, $oldPassword = null, $ignoreOldPassword = false)
    {
        if (!$ignoreOldPassword && empty($oldPassword)) {
            return Response::generate(-1, '旧密码不能为空');
        }

        $memberUser = ModelUtil::get('member_user', ['id' => $memberUserId]);
        if (empty($memberUser)) {
            return Response::generate(-1, "用户不存在");
        }
        if (empty ($newPassword)) {
            return Response::generate(-1, '新密码为空');
        }
        $ret = self::passwordStrengthCheck($newPassword);
        if (Response::isError($ret)) {
            return Response::generate(-1, $ret['msg']);
        }
        if (!$ignoreOldPassword && EncodeUtil::md5WithSalt($oldPassword, $memberUser['passwordSalt']) != $memberUser['password']) {
            return Response::generate(-1, '旧密码不正确');
        }

        $passwordSalt = Str::random(16);

        ModelUtil::update('member_user', ['id' => $memberUser['id']], [
            'passwordSalt' => $passwordSalt,
            'password' => EncodeUtil::md5WithSalt($newPassword, $passwordSalt)
        ]);

        return Response::generate(0, 'ok');
    }

    /**
     * 用户上传你图片
     * @param $userId
     * @param $avatarData
     * @param string $avatarExt
     * @return array ['code'=>'0','msg'=>'ok']
     * @throws \Exception
     */
    public static function setAvatar($userId, $avatarData, $avatarExt = 'jpg')
    {
        if (!in_array($avatarExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            return Response::generate(-1, '图片格式不正确');
        }
        $memberUser = self::get($userId);
        if (empty($memberUser)) {
            return Response::generate(-1, '用户不存在');
        }
        if (empty($avatarData)) {
            return Response::generate(-1, '图片数据为空');
        }
        $imageBig = (string)Image::make($avatarData)->resize(400, 400)->encode($avatarExt, 75);
        $imageMedium = (string)Image::make($avatarData)->resize(200, 200)->encode($avatarExt, 75);
        $image = (string)Image::make($avatarData)->resize(50, 50)->encode($avatarExt, 75);

        $uploadParam = [
            'eventOpt' => [
                'param' => [
                    'userType' => 'member',
                    'userId' => $userId
                ],
                DataFileUploadedEvent::OPT_IMAGE_COMPRESS_IGNORE => true,
                DataFileUploadedEvent::OPT_IMAGE_WATERMARK_IGNORE => true,
            ]
        ];
        $retBig = DataManager::upload('image', 'U' . $userId . '_AvatarBig.' . $avatarExt, $imageBig, null, $uploadParam);
        if ($retBig['code']) {
            return Response::generate(-1, '头像存储失败（' . $retBig['msg'] . '）');
        }
        $retMedium = DataManager::upload('image', 'U' . $userId . '_AvatarMiddle.' . $avatarExt, $imageMedium, null, $uploadParam);
        if ($retMedium['code']) {
            DataManager::deleteById($retBig['data']['id']);
            if ($retBig['code']) {
                return Response::generate(-1, '头像存储失败（' . $retMedium['msg'] . '）');
            }
        }
        $ret = DataManager::upload('image', 'U_' . $userId . '_Avatar.' . $avatarExt, $image, null, $uploadParam);
        if ($ret['code']) {
            DataManager::deleteById($retBig['data']['id']);
            DataManager::deleteById($retMedium['data']['id']);
            if ($retBig['code']) {
                return Response::generate(-1, '头像存储失败（' . $ret['msg'] . '）');
            }
        }
        self::update($memberUser['id'], [
            'avatarBig' => $retBig['data']['fullPath'],
            'avatarMedium' => $retMedium['data']['fullPath'],
            'avatar' => $ret['data']['fullPath']
        ]);
        return Response::generateSuccess();
    }

    /**
     * 批量查询用户
     * @param $userIds
     * @return array [userId=>MemberUser,...]
     */
    public static function findUsers($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        $userMemberMap = [];
        $memberUsers = ModelUtil::model('member_user')->whereIn('id', $userIds)->get();
        foreach ($memberUsers as &$r) {
            $userMemberMap[$r->id] = $r->toArray();
        }
        return $userMemberMap;
    }

    /**
     * 过滤用户ID为真实用户ID
     * @param $userIds
     * @return int[]
     */
    public static function filterUserIds($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        $map = [];
        $memberUsers = ModelUtil::model('member_user')->whereIn('id', $userIds)->get(['id']);
        foreach ($memberUsers as &$r) {
            $map[$r->id] = true;
        }
        return array_keys($map);
    }

    public static function mergeMemberUsers(&$records, $memberUserIdKey = 'memberUserId', $memberUserMergeKey = '_memberUser')
    {
        ModelUtil::join($records, $memberUserIdKey, $memberUserMergeKey, 'member_user', 'id');
    }

    public static function mergeMemberUserBasics(&$records, $memberUserIdKey = 'memberUserId', $memberUserMergeKey = '_memberUser', $keepFields = null)
    {
        $keepFields = self::processBasicFields($keepFields);
        if (is_array($records)) {
            ModelUtil::join($records, $memberUserIdKey, $memberUserMergeKey, MemberUser::class, 'id');
            foreach ($records as $k => $v) {
                if (empty($v[$memberUserMergeKey])) {
                    continue;
                }
                $viewName = self::viewName($v[$memberUserMergeKey]);
                $memberUser = ArrayUtil::keepKeys($v[$memberUserMergeKey], $keepFields);
                if (empty($memberUser['nickname'])) {
                    $memberUser['nickname'] = $memberUser['username'];
                }
                if (empty($memberUser['avatar'])) {
                    $memberUser['avatar'] = AssetsUtil::fixFull('asset/image/avatar.svg');
                } else {
                    $memberUser['avatar'] = AssetsUtil::fixFull($memberUser['avatar']);
                }
                $memberUser['viewName'] = $viewName;
                $records[$k][$memberUserMergeKey] = $memberUser;
            }
        } else {
            ModelUtil::joinItems($records, $memberUserIdKey, $memberUserMergeKey, MemberUser::class, 'id');
            foreach ($records as $item) {
                if (empty($item->{$memberUserMergeKey})) {
                    continue;
                }
                $viewName = self::viewName($item->{$memberUserMergeKey});
                $memberUser = ArrayUtil::keepKeys($item->{$memberUserMergeKey}, $keepFields);
                $memberUser['nickname'] = $memberUser['username'];
                if (empty($memberUser['avatar'])) {
                    $memberUser['avatar'] = AssetsUtil::fixFull('asset/image/avatar.svg');
                } else {
                    $memberUser['avatar'] = AssetsUtil::fixFull($memberUser['avatar']);
                }
                $memberUser['viewName'] = $viewName;
                $item->{$memberUserMergeKey} = $memberUser;
            }
        }
    }

    public static function insert($data)
    {
        return ModelUtil::insert('member_user', $data);
    }

    public static function getIdByOauth($oauthType, $openId)
    {
        $m = ModelUtil::get('member_oauth', ['type' => $oauthType, 'openId' => $openId]);
        if (empty($m)) {
            return 0;
        }
        return intval($m['memberUserId']);
    }

    public static function getIdByOauthAndCheck($oauthType, $openId)
    {
        $memberUserId = self::getIdByOauth($oauthType, $openId);
        if (self::get($memberUserId)) {
            return $memberUserId;
        }
        MemberUtil::forgetOauth($oauthType, $openId);
        return 0;
    }

    public static function getOauthOpenId($memberUserId, $oauthType)
    {
        $where = ['memberUserId' => $memberUserId, 'type' => $oauthType];
        $m = ModelUtil::get('member_oauth', $where);
        if (empty($m)) {
            return null;
        }
        return $m['openId'];
    }

    /**
     * @param $memberUserId
     * @param $oauthType
     * @return array|null
     * @since Member 1.6.0
     */
    public static function getOauth($memberUserId, $oauthType)
    {
        $where = ['memberUserId' => $memberUserId, 'type' => $oauthType];
        return ModelUtil::get('member_oauth', $where);
    }

    public static function listOauths($memberUserId)
    {
        return ModelUtil::all('member_oauth', ['memberUserId' => $memberUserId], ['*'], ['type', 'asc']);
    }

    public static function putOauth($memberUserId, $oauthType, $openId, $info = [])
    {
        $where = ['memberUserId' => $memberUserId, 'type' => $oauthType];
        $lockKey = "MemberOauth:$memberUserId";
        if (!LockUtil::acquire($lockKey)) {
            BizException::throws('正在处理中，请稍后再试');
        }
        $m = ModelUtil::get('member_oauth', $where);
        $update = array_merge(['openId' => $openId], $info);
        if (empty($m)) {
            ModelUtil::delete('member_oauth', ['type' => $oauthType, 'openId' => $openId]);
            ModelUtil::insert('member_oauth', array_merge($where, $update));
        } else if ($m['openId'] != $openId) {
            ModelUtil::update('member_oauth', $m['id'], $update);
        }
        LockUtil::release($lockKey);
    }

    public static function forgetOauthByMemberUserId($memberUserId)
    {
        ModelUtil::delete('member_oauth', ['memberUserId' => $memberUserId]);
    }

    public static function forgetOauth($oauthType, $openId)
    {
        ModelUtil::delete('member_oauth', ['type' => $oauthType, 'openId' => $openId]);
    }

    public static function updateNewMessageStatus($memberUserId)
    {
        ModelUtil::update('member_user', ['id' => $memberUserId], [
            'newMessageCount' => ModelUtil::count('member_message', [
                'userId' => $memberUserId,
                'status' => MemberMessageStatus::UNREAD,
            ])
        ]);
    }

    public static function updateNewChatMsgStatus($memberUserId)
    {
        if (modstart_module_enabled('MemberChat')) {
            ModelUtil::update('member_user', ['id' => $memberUserId], [
                'newChatMsgCount' => ModelUtil::sum('member_chat', 'unreadMsgCount', [
                    'memberUserId' => $memberUserId,
                ])
            ]);
        }
    }

    public static function paginate($page, $pageSize, $option = [])
    {
        return ModelUtil::paginate('member_user', $page, $pageSize, $option);
    }

    public static function updateStatus($memberUserIds, $status)
    {
        if (!is_array($memberUserIds)) {
            $memberUserIds = [$memberUserIds];
        }
        if (empty($memberUserIds)) {
            return;
        }
        ModelUtil::model('member_user')->whereIn('id', $memberUserIds)->update(['status' => $status]);
    }

    public static function delete($memberUserId)
    {
        $memberUser = self::get($memberUserId);
        BizException::throwsIfEmpty('用户不存在', $memberUser);
        ModelUtil::transactionBegin();
        $content = [];
        $oauths = ModelUtil::all('member_oauth', [
            'memberUserId' => $memberUser['id'],
        ]);
        $content['oauth'] = ArrayUtil::keepItemsKeys($oauths, [
            'type', 'openId', 'infoUsername', 'infoAvatar'
        ]);
        $content['nickname'] = $memberUser['nickname'];
        ModelUtil::insert('member_deleted', [
            'id' => $memberUser['id'],
            'username' => $memberUser['username'],
            'phone' => $memberUser['phone'],
            'email' => $memberUser['email'],
            'content' => SerializeUtil::jsonEncode($content),
        ]);
        ModelUtil::update('member_user', $memberUserId, [
            'deleteAtTime' => 0,
            'isDeleted' => true,
            'username' => null,
            'nickname' => null,
            'phone' => null,
            'email' => null,
        ]);
        self::forgetOauthByMemberUserId($memberUserId);
        ModelUtil::transactionCommit();
        MemberUserDeletedEvent::fire($memberUserId);
    }

    public static function fireLogin($memberUserId)
    {
        $ip = Request::ip();
        ModelUtil::update('member_user', $memberUserId, [
            'lastLoginTime' => Carbon::now(),
            'lastLoginIp' => StrUtil::mbLimit($ip, 20),
        ]);
        ModelUtil::insert('member_login_log', [
            'memberUserId' => $memberUserId,
            'deviceType' => DeviceType::current(),
            'ip' => StrUtil::mbLimit($ip, 20),
            'userAgent' => StrUtil::mbLimit(AgentUtil::getUserAgent(), 400),
        ]);
    }

    public static function passwordStrengthCheck($password)
    {
        $lengthMin = modstart_config('Member_PasswordLengthMin', 0);
        if ($lengthMin > 0) {
            if (strlen($password) < $lengthMin) {
                return Response::generateError('密码长度不能小于' . $lengthMin . '位');
            }
        }
        $strength = modstart_config('Member_PasswordStrength', MemberPasswordStrength::NO_LIMIT);
        switch ($strength) {
            case MemberPasswordStrength::NO_LIMIT:
                break;
            case MemberPasswordStrength::STRENGTH_2:
                if (StrUtil::passwordStrength($password) < 2) {
                    return Response::generateError('密码必须包含 大写/小写/数字/特殊字符 2种以上');
                }
                break;
            case MemberPasswordStrength::STRENGTH_3:
                if (StrUtil::passwordStrength($password) < 3) {
                    return Response::generateError('密码必须包含 大写/小写/数字/特殊字符 3种以上');
                }
                break;
        }
        return Response::generateSuccess();
    }

}
