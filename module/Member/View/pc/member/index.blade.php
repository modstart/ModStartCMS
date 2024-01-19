@extends($_viewMemberFrame)

@section('pageTitleMain'){{'我的'}}@endsection

@section('memberBodyContent')

    <div class="tw-bg-white tw-rounded-lg margin-bottom">
        <div class="tw-flex tw-p-4 tw-flex-wrap tw-items-center">
            <div class="tw-flex-shrink-0 tw-w-24">
                <a href="{{modstart_web_url('member_profile/avatar')}}"
                   class="tw-block tw-w-16 tw-h-16 ub-cover-1-1 tw-rounded-full tw-shadow"
                   style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($_memberUser['avatar'])}})"></a>
            </div>
            <div class="tw-flex-grow">
                <div class="tw-font-bold tw-text-lg">
                    {{\Module\Member\Auth\MemberUser::viewName()}}
                    ，欢迎您！
                    @if(\ModStart\Module\ModuleManager::getModuleConfig('Member', 'vipEnable',false))
                        <a class="tw-my-1 tw-mr-1 ub-tag warning ub-cursor-pointer" href="{{modstart_web_url('member_vip')}}">
                            @if(\Module\Member\Auth\MemberVip::get('icon'))
                                <img src="{{\ModStart\Core\Assets\AssetsUtil::fix(\Module\Member\Auth\MemberVip::get('icon'))}}"
                                     class="tw-h-4 tw-w-4 tw-overflow-hidden" />
                            @endif
                            {{\Module\Member\Auth\MemberVip::get('title')}}
                        </a>
                    @endif
                    @if($_certType!==null)
                        @if($_certType==\Module\MemberCert\Type\CertType::INDIVIDUAL)
                            <a class="tw-my-1 tw-mr-1 ub-tag success ub-cursor-pointer" href="{{modstart_web_url('member_cert')}}">
                                <i class="iconfont icon-user"></i>
                                个人认证
                            </a>
                        @elseif($_certType==\Module\MemberCert\Type\CertType::CORP)
                            <a class="tw-my-1 tw-mr-1 ub-tag success ub-cursor-pointer" href="{{modstart_web_url('member_cert')}}">
                                <i class="iconfont icon-corp"></i>
                                企业认证
                            </a>
                        @else
                            <a class="tw-my-1 tw-mr-1 ub-tag ub-cursor-pointer" href="{{modstart_web_url('member_cert')}}">
                                <i class="iconfont icon-question"></i>
                                未认证
                            </a>
                        @endif
                    @endif
                </div>
                <div class="tw-text-gray-400 tw-pt-1">
                    {{empty($_memberUser['signature'])?'暂无签名':$_memberUser['signature']}}
                </div>
            </div>
        </div>
    </div>

    <div class="margin-bottom">
        {!! $content !!}
    </div>

@endsection
