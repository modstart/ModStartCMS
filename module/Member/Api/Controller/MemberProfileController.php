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
use ModStart\Misc\Captcha\CaptchaFacade;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Config\MemberOauth;
use Module\Member\Events\MemberUserUpdatedEvent;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberUtil;
use Module\Vendor\Email\MailSendJob;
use Module\Vendor\Sms\SmsUtil;
use Module\Vendor\Support\ResponseCodes;

class MemberProfileController extends ModuleBaseController implements MemberLoginCheck
{
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

    public function avatar()
    {
        $input = InputPackage::buildFromInput();
        $avatar = $input->get('avatar');
        if (empty($avatar)) {
            return Response::generate(-1, '头像内容为空');
        }
        switch ($input->getTrimString('type')) {
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
        SmsUtil::send($phone, 'verify', ['code' => $verify]);
        return Response::generate(0, '验证码发送成功');

    }

    public function oauthUnbind()
    {
        $input = InputPackage::buildFromInput();
        $type = $input->getTrimString('type');
        $oauth = MemberOauth::getOrFail($type);
        BizException::throwsIfEmpty('授权方式不存在', $oauth);
        $openId = MemberUtil::getOauthOpenId(MemberUser::id(), $oauth->name());
        if ($openId) {
            MemberUtil::forgetOauth($oauth->name(), $openId);
        }
        return Response::generate(0, '解绑成功', null, '[reload]');
    }
}
