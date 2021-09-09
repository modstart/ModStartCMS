<?php

namespace App\Api\Controller;

use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Type\TypeUtil;
use ModStart\Core\Util\ConstantUtil;
use Module\Doc\Type\DocType;
use Module\Doc\Type\DocVisit;
use Module\Hire\Type\WorkApplyStatus;
use Module\Hire\Type\WorkGenderType;
use Module\Hire\Type\WorkHeightType;
use Module\Hire\Type\WorkInviteStatus;
use Module\Hire\Type\WorkSalaryCashPeriod;
use Module\Hire\Type\WorkSalaryPeriod;
use Module\Hire\Type\WorkSalaryType;
use Module\Hire\Type\WorkStatus;
use Module\Member\Auth\MemberUser;
use Module\Member\Auth\MemberVip;
use Module\Member\Type\Education;
use Module\Member\Type\Gender;
use Module\Member\Type\MemberMessageStatus;
use Module\MemberCert\Type\CorpStatus;
use Module\MemberCert\Type\IndividualStatus;
use Module\NoteSns\Type\NoteEditorType;
use Module\Question\Api\Controller\QuestionTagController;
use Module\Shop\Type\GoodsSaleStatus;
use Module\Vendor\Cache\LazyValueUtil;
use Module\Vendor\Support\ResponseCodes;

class ConfigController extends BaseController
{
    public function constant()
    {
        $constants = [];
        $constants['WorkApplyStatus'] = TypeUtil::dump(WorkApplyStatus::class);
        $constants['WorkGenderType'] = TypeUtil::dump(WorkGenderType::class);
        $constants['WorkHeightType'] = TypeUtil::dump(WorkHeightType::class);
        $constants['WorkInviteStatus'] = TypeUtil::dump(WorkInviteStatus::class);
        $constants['WorkSalaryCashPeriod'] = TypeUtil::dump(WorkSalaryCashPeriod::class);
        $constants['WorkSalaryPeriod'] = TypeUtil::dump(WorkSalaryPeriod::class);
        $constants['WorkSalaryType'] = TypeUtil::dump(WorkSalaryType::class);
        $constants['WorkStatus'] = TypeUtil::dump(WorkStatus::class);
        $constants['CorpStatus'] = TypeUtil::dump(CorpStatus::class);
        $constants['IndividualStatus'] = TypeUtil::dump(IndividualStatus::class);
        $constants['Education'] = TypeUtil::dump(Education::class);
        $constants['Gender'] = TypeUtil::dump(Gender::class);
        $content = [];
        $content [] = "// This file is created by " . action('\\' . __CLASS__ . '@constant') . "\n";
        foreach ($constants as $name => $json) {
            $content[] = "export const $name = " . json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . ";";
        }
        return Response::raw(join("\n", $content), ['Content-Type' => 'text/plain']);
    }

    public function app()
    {
        $data = [];
        list($view, $_) = $this->viewPaths('index');
        $hash = date('Ymd_His', filemtime($this->viewRealpath($view)));
        $data['hashPC'] = 'v' . $hash;
        $data['hashLazyValue'] = [];

        $data['user'] = [
            'id' => 0,
            'avatar' => AssetsUtil::fixFull('asset/image/avatar.png'),
            'avatarMedium' => AssetsUtil::fixFull('asset/image/avatar.png'),
            'avatarBig' => AssetsUtil::fixFull('asset/image/avatar.png'),
            'nickname' => '',
            'username' => '',
            'phone' => '',
            'phoneVerified' => false,
            'email' => '',
            'emailVerified' => false,
            'vip' => null,
            'vipExpire' => null,
        ];
        if (MemberUser::id()) {
            $memberUser = MemberUser::user();
            $data['user']['id'] = $memberUser['id'];
            $data['user']['avatar'] = AssetsUtil::fixFull($memberUser['avatar'] ? $memberUser['avatar'] : $data['user']['avatar']);
            $data['user']['avatarMedium'] = AssetsUtil::fixFull($memberUser['avatarMedium'] ? $memberUser['avatarMedium'] : $data['user']['avatar']);
            $data['user']['avatarBig'] = AssetsUtil::fixFull($memberUser['avatarBig'] ? $memberUser['avatarBig'] : $data['user']['avatar']);
            $data['user']['username'] = $memberUser['username'];
            $data['user']['nickname'] = empty($memberUser['nickname']) ? null : $memberUser['nickname'];
            if (empty($data['user']['nickname'])) {
                $data['user']['nickname'] = $data['user']['username'];
            }
            $data['user']['phone'] = $memberUser['phone'];
            $data['user']['phoneVerified'] = !!$memberUser['phoneVerified'];
            $data['user']['email'] = $memberUser['email'];
            $data['user']['emailVerified'] = !!$memberUser['emailVerified'];
            $data['user']['vip'] = MemberVip::get();
            $data['user']['vipExpire'] = $memberUser['vipExpire'];
        }

        return Response::jsonSuccessData($data);
    }


}
