<?php


namespace Module\Member\Api\Controller;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\EventUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\FormatUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\Misc\Captcha\CaptchaFacade;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Config\MemberOauth;
use Module\Member\Events\MemberUserUpdatedEvent;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberUtil;
use Module\Vendor\Job\MailSendJob;
use Module\Vendor\Job\SmsSendJob;
use Module\Vendor\Support\ResponseCodes;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MemberProfileController
 * @package Module\Member\Api\Controller
 * @Api 用户资料
 */
class MemberProfileController extends ModuleBaseController implements MemberLoginCheck
{
    public function nickname()
    {
        $input = InputPackage::buildFromInput();
        $nickname = $input->getTrimString('nickname');
        BizException::throwsIfEmpty('昵称为空', $nickname);
        if (!CaptchaFacade::check($input->getTrimString('captcha'))) {
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
        }
        $ret = MemberUtil::changeNickname(MemberUser::id(), $nickname);
        BizException::throwsIfResponseError($ret);
        EventUtil::fire(new MemberUserUpdatedEvent(MemberUser::id(), 'nickname'));
        return Response::generate(0, '修改成功', null, '[reload]');
    }

    /**
     * @Api 修改密码
     * @ApiBodyParam passwordOld string required 原密码
     * @ApiBodyParam passwordNew string required 新密码
     * @ApiBodyParam passwordRepeat string required 重复新密码
     */
    public function password()
    {
        $input = InputPackage::buildFromInput();
        $passwordOld = $input->getTrimString('passwordOld');
        $passwordNew = $input->getTrimString('passwordNew');
        $passwordRepeat = $input->getTrimString('passwordRepeat');
        if ($passwordNew != $passwordRepeat) {
            return Response::generate(-1, '两次新密码输入不一致');
        }
        $memberUser = MemberUser::get();
        if (empty($memberUser['password'])) {
            $ret = MemberUtil::changePassword(MemberUser::id(), $passwordNew, null, true);
        } else {
            $ret = MemberUtil::changePassword(MemberUser::id(), $passwordNew, $passwordOld);
        }
        if ($ret['code']) {
            return Response::generate(-1, $ret['msg']);
        }
        EventUtil::fire(new MemberUserUpdatedEvent(MemberUser::id(), 'password'));
        return Response::generate(0, '修改成功', null, '[reload]');
    }

    /**
     * @Api 修改用户头像
     * @ApiBodyParam avatar string required base64头像
     * @ApiBodyParam type string required 类型，固定为 cropper
     */
    public function avatar()
    {
        $input = InputPackage::buildFromInput();
        $avatar = $input->getTrimString('avatar');
        if (empty($avatar)) {
            return Response::generate(-1, '头像内容为空');
        }
        switch ($input->getTrimString('type')) {
            case 'file':
                /** @var UploadedFile $avatarFile */
                $avatarFile = Input::file('avatar');
                BizException::throwsIfEmpty('头像文件为空', $avatarFile);
                $ext = FileUtil::mimeToExt($avatarFile->getClientMimeType());
                BizException::throwsIf('头像格式不合法', !in_array($ext, ['jpg', 'png', 'jpeg']));
                $content = file_get_contents($avatarFile->getRealPath());
                BizException::throwsIfEmpty('头像内容为空', $content);
                $ret = MemberUtil::setAvatar(MemberUser::id(), $content, $ext);
                if ($ret['code']) {
                    return $ret;
                }
                EventUtil::fire(new MemberUserUpdatedEvent(MemberUser::id(), 'avatar'));
                return Response::generate(0, '保存成功', null, '[reload]');
            case 'cropper':
                $avatarType = null;
                if (Str::startsWith($avatar, 'data:image/jpeg;base64,')) {
                    $avatarType = 'jpg';
                    $avatar = substr($avatar, strlen('data:image/jpeg;base64,'));
                } else if (Str::startsWith($avatar, 'data:image/png;base64,')) {
                    $avatarType = 'png';
                    $avatar = substr($avatar, strlen('data:image/png;base64,'));
                }
                if (empty($avatarType)) {
                    return Response::generate(-1, '头像数据为空');
                }
                $avatar = @base64_decode($avatar);
                if (empty($avatar)) {
                    return Response::generate(-1, '头像内容为空');
                }
                $ret = MemberUtil::setAvatar(MemberUser::id(), $avatar, $avatarType);
                if ($ret['code']) {
                    return $ret;
                }
                EventUtil::fire(new MemberUserUpdatedEvent(MemberUser::id(), 'avatar'));
                return Response::generate(0, '保存成功', null, '[reload]');
            default:
                $avatar = FileUtil::savePathToLocalTemp($avatar);
                if (empty($avatar)) {
                    return Response::generate(-1, '读取头像文件失败:-1');
                }
                $avatarExt = FileUtil::extension($avatar);
                if (!in_array($avatarExt, config('data.upload.image.extensions'))) {
                    return Response::generate(-1, '头像格式不合法');
                }
                $avatar = FileUtil::safeGetContent($avatar);
                if (empty($avatar)) {
                    return Response::generate(-1, '读取头像文件失败:-2');
                }
                $ret = MemberUtil::setAvatar(MemberUser::id(), $avatar, $avatarExt);
                if ($ret['code']) {
                    return $ret;
                }
                EventUtil::fire(new MemberUserUpdatedEvent(MemberUser::id(), 'avatar'));
                return Response::generate(0, '保存成功', null, '[reload]');
        }
    }

    public function captchaRaw()
    {
        return CaptchaFacade::create('default');
    }

    /**
     * @Api 获取图片验证码（修改手机、修改邮箱）
     */
    public function captcha()
    {
        $captcha = $this->captchaRaw();
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }

    public function email()
    {
        $input = InputPackage::buildFromInput();
        $email = $input->getEmail('email');
        $verify = $input->getTrimString('verify');

        if (empty($email)) {
            return Response::generate(-1, '邮箱不能为空');
        }
        if (!FormatUtil::isEmail($email)) {
            return Response::generate(-1, '邮箱格式不正确');
        }
        if (empty($verify)) {
            return Response::generate(-1, '验证码不能为空');
        }
        if ($verify != Session::get('memberProfileEmailVerify')) {
            return Response::generate(-1, '验证码不正确');
        }
        if (Session::get('memberProfileEmailVerifyTime') + 60 * 60 < time()) {
            return Response::generate(0, '验证码已过期');
        }
        if ($email != Session::get('memberProfileEmail')) {
            return Response::generate(-1, '两次邮箱不一致');
        }
        $memberUserExists = MemberUtil::getByEmail($email);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != MemberUser::id()) {
                return Response::generate(-1, '该邮箱已被其他账户绑定');
            }
            if ($memberUserExists['id'] == MemberUser::id() && $memberUserExists['email'] == $email) {
                if (!empty($memberUserExists['emailVerified'])) {
                    return Response::generate(-1, '邮箱未修改，无需重新绑定。');
                }
            }
        }
        MemberUtil::update(MemberUser::id(), [
            'emailVerified' => true,
            'email' => $email,
        ]);
        EventUtil::fire(new MemberUserUpdatedEvent(MemberUser::id(), 'email'));
        Session::forget('memberProfileEmailVerify');
        Session::forget('memberProfileEmailVerifyTime');
        Session::forget('memberProfileEmail');
        return Response::generate(0, '修改成功', null, '[reload]');
    }

    public function emailVerify()
    {
        $email = Input::get('target');
        if (empty($email)) {
            return Response::generate(-1, '邮箱不能为空');
        }
        if (!FormatUtil::isEmail($email)) {
            return Response::generate(-1, '邮箱格式不正确');
        }

        if (!CaptchaFacade::check(Input::get('captcha'))) {
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
        }

        $memberUserExists = MemberUtil::getByEmail($email);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != MemberUser::id()) {
                return Response::generate(-1, '该邮箱已被其他账户绑定');
            }
            if ($memberUserExists['id'] == MemberUser::id() && $memberUserExists['email'] == $email) {
                if (!empty($memberUserExists['emailVerified'])) {
                    return Response::generate(-1, '邮箱未修改，无需重新绑定。');
                }
            }
        }
        if (Session::get('memberProfileEmailVerifyTime') && $email == Session::get('memberProfileEmail')) {
            if (Session::get('memberProfileEmailVerifyTime') + 60 * 10 > time()) {
                return Response::generate(0, '验证码发送成功!');
            }
        }
        $verify = rand(100000, 999999);
        Session::put('memberProfileEmailVerify', $verify);
        Session::put('memberProfileEmailVerifyTime', time());
        Session::put('memberProfileEmail', $email);
        MailSendJob::create($email, '验证码', 'verify', ['code' => $verify]);
        return Response::generate(0, '验证码发送成功');
    }

    /**
     * @Api 修改手机号码
     * @ApiBodyParam phone string 手机号码
     * @ApiBodyParam verify string 手机验证码
     */
    public function phone()
    {
        $input = InputPackage::buildFromInput();
        $phone = $input->getPhone('phone');
        $verify = $input->getTrimString('verify');
        if (empty($phone)) {
            return Response::generate(-1, '手机不能为空');
        }
        if (!FormatUtil::isPhone($phone)) {
            return Response::generate(-1, '手机格式不正确');
        }
        if (empty($verify)) {
            return Response::generate(-1, '验证码不能为空');
        }
        if ($verify != Session::get('memberProfilePhoneVerify')) {
            return Response::generate(-1, '验证码不正确');
        }
        if (Session::get('memberProfilePhoneVerifyTime') + 60 * 60 < time()) {
            return Response::generate(0, '验证码已过期');
        }
        if ($phone != Session::get('memberProfilePhone')) {
            return Response::generate(-1, '两次手机不一致');
        }
        $memberUserExists = MemberUtil::getByPhone($phone);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != MemberUser::id()) {
                return Response::generate(-1, '该手机已被其他账户绑定');
            }
            if ($memberUserExists['id'] == MemberUser::id() && $memberUserExists['phone'] == $phone) {
                if (!empty($memberUserExists['phoneVerified'])) {
                    return Response::generate(-1, '手机号未修改，无需重新绑定。');
                }
            }
        }
        MemberUtil::update(MemberUser::id(), [
            'phoneVerified' => true,
            'phone' => $phone,
        ]);
        EventUtil::fire(new MemberUserUpdatedEvent(MemberUser::id(), 'phone'));
        Session::forget('memberProfilePhoneVerify');
        Session::forget('memberProfilePhoneVerifyTime');
        Session::forget('memberProfilePhone');
        return Response::generate(0, '修改成功', null, '[reload]');
    }

    /**
     * @Api 发送手机验证码
     * @ApiBodyParam target string 手机号码
     * @ApiBodyParam captcha string 图片验证码
     */
    public function phoneVerify()
    {
        $phone = Input::get('target');
        if (empty($phone)) {
            return Response::generate(-1, '手机不能为空');
        }
        if (!FormatUtil::isPhone($phone)) {
            return Response::generate(-1, '手机格式不正确');
        }
        if (!CaptchaFacade::check(Input::get('captcha'))) {
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '图片验证码错误');
        }
        $memberUserExists = MemberUtil::getByPhone($phone);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != MemberUser::id()) {
                return Response::generate(-1, '该手机已被其他账户绑定');
            }
            if ($memberUserExists['id'] == MemberUser::id() && $memberUserExists['phone'] == $phone) {
                if (!empty($memberUserExists['phoneVerified'])) {
                    return Response::generate(-1, '手机号未修改，无需重新绑定。');
                }
            }
        }

        if (Session::get('memberProfilePhoneVerifyTime') && $phone == Session::get('memberProfilePhone')) {
            if (Session::get('memberProfilePhoneVerifyTime') + 60 * 2 > time()) {
                return Response::generate(0, '验证码发送成功!');
            }
        }
        $verify = rand(100000, 999999);
        Session::put('memberProfilePhoneVerify', $verify);
        Session::put('memberProfilePhoneVerifyTime', time());
        Session::put('memberProfilePhone', $phone);
        SmsSendJob::create($phone, 'verify', ['code' => $verify]);
        return Response::generate(0, '验证码发送成功');

    }

    public function oauthUnbind()
    {
        $input = InputPackage::buildFromInput();
        $type = $input->getTrimString('type');
        $oauth = MemberOauth::getOrFail($type);
        BizException::throwsIfEmpty('授权方式不存在', $oauth);
        $openId = MemberUtil::getOauthOpenId(MemberUser::id(), $oauth->oauthKey());
        if ($openId) {
            MemberUtil::forgetOauth($oauth->oauthKey(), $openId);
        }
        return Response::generate(0, '解绑成功', null, '[reload]');
    }

    public function deleteInfo()
    {
        if (!modstart_config('Member_DeleteEnable', false)) {
            return Response::generateError('注销账号功能未开启');
        }
        $memberUser = MemberUser::get();
        $data = [];
        $data['deleteAtTime'] = null;
        if ($memberUser['deleteAtTime'] > 0) {
            $data['deleteAtTime'] = date('Y-m-d H:i:s', $memberUser['deleteAtTime']);
        }
        $data['registerTime'] = $memberUser['created_at'];
        return Response::generateSuccessData($data);
    }

    /**
     * @Api 账号注销申请
     * @ApiBodyParam agree string 同意协议选项，固定yes
     */
    public function delete()
    {
        if (!modstart_config('Member_DeleteEnable', false)) {
            return Response::generateError('注销账号功能未开启');
        }
        $memberUser = MemberUser::get();
        if ($memberUser['deleteAtTime'] > 0) {
            return Response::generateError('账号正在注销中');
        }
        $input = InputPackage::buildFromInput();
        $agree = $input->getTrimString('agree');
        BizException::throwsIf('请勾选同意选项', $agree != 'yes');
        MemberUtil::update(MemberUser::id(), [
            'deleteAtTime' => time() + TimeUtil::PERIOD_MONTH,
        ]);
        return Response::generate(0, '申请注销成功', null, '[reload]');
    }

    /**
     * @Api 账号注销申请撤销
     */
    public function deleteRevert()
    {
        if (!modstart_config('Member_DeleteEnable', false)) {
            return Response::generateError('注销账号功能未开启');
        }
        $memberUser = MemberUser::get();
        if (empty($memberUser['deleteAtTime'])) {
            return Response::generateError('账号没有注销操作');
        }
        MemberUtil::update(MemberUser::id(), [
            'deleteAtTime' => 0
        ]);
        return Response::generate(0, '撤销操作成功', null, '[reload]');
    }
}
