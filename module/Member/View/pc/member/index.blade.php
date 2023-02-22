@extends($_viewMemberFrame)

@section('pageTitleMain'){{'我的'}}@endsection

@section('memberBodyContent')
    <div class="tw-bg-white tw-rounded-lg">
        <div class="tw-flex tw-p-4 tw-flex-wrap">
            <div class="tw-flex-shrink-0 tw-w-14">
                <a href="{{modstart_web_url('member_profile/avatar')}}"
                   class="tw-block tw-w-10 tw-h-10 ub-cover-1-1 tw-rounded-full tw-shadow"
                   style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($_memberUser['avatar'])}})"></a>
            </div>
            <div class="tw-flex-grow">
                <div class="tw-text-bold tw-text-lg">{{\Module\Member\Auth\MemberUser::nickname()}}</div>
                <div>
                    @if(\ModStart\Module\ModuleManager::getModuleConfigBoolean('Member', 'vipEnable'))
                        <span class="tw-my-1 tw-mr-2 tw-inline-block ub-text-primary">
                            @if(\Module\Member\Auth\MemberVip::get('icon'))
                                <img src="{{\ModStart\Core\Assets\AssetsUtil::fix(\Module\Member\Auth\MemberVip::get('icon'))}}"
                                     class="tw-h-4 tw-w-4 tw-overflow-hidden" />
                            @endif
                            {{\Module\Member\Auth\MemberVip::get('title')}}
                        </span>
                    @endif
                    @if(modstart_module_enabled('MemberCert'))
                        <?php $certType = \Module\MemberCert\Util\MemberCertUtil::getCertType(\Module\Member\Auth\MemberUser::id()); ?>
                        @if($certType==\Module\MemberCert\Type\CertType::INDIVIDUAL)
                            <span class="tw-my-1 tw-mr-2 tw-inline-block ub-text-primary">
                                <i class="iconfont icon-user"></i>
                                个人认证
                            </span>
                        @endif
                        @if($certType==\Module\MemberCert\Type\CertType::CORP)
                            <span class="tw-my-1 tw-mr-2 tw-inline-block ub-text-primary">
                                <i class="iconfont icon-corp"></i>
                                企业认证
                            </span>
                        @endif
                    @endif
                </div>
                <div class="tw-text-gray-400">{{empty($_memberUser['signature'])?'暂无签名':$_memberUser['signature']}}</div>
{{--                <div class="tw-flex tw-mt-3">--}}
{{--                    <a href="{{modstart_web_url('wenda/member/'.$memberUser['id'].'/replies')}}" class="tw-text-center tw-pr-4 tw-text-sm">--}}
{{--                        <div class="tw-text-gray-300">回答</div>--}}
{{--                        <div class="tw-text-gray-900">{{$memberUser['_replyCount'] or '0'}}</div>--}}
{{--                    </a>--}}
{{--                </div>--}}
            </div>
{{--            <div class="tw-flex tw-items-start tw-w-full tw-flex-shrink-0 tw-ml-20 tw-mt-4 lg:tw-w-auto">--}}
{{--                <a href="{{modstart_web_url('wenda/ask/edit',['invite'=>$memberUser['id']])}}" class="btn btn-primary tw-mr-1">--}}
{{--                    <i class="iconfont icon-comment"></i>--}}
{{--                    提问--}}
{{--                </a>--}}
{{--            </div>--}}
        </div>
    </div>

    <div class="margin-top">
        {!! $content !!}
    </div>

@endsection
