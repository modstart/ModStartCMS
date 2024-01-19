@extends($_viewFrameDialog)

@section('pageTitleMain')开通VIP会员@endsection
@section('pageKeywords')开通VIP会员@endsection
@section('pageDescription')开通VIP会员@endsection

{!! \ModStart\ModStart::css('vendor/Member/style/member.css') !!}
{!! \ModStart\ModStart::css('asset/vendor/swiper/swiper.css') !!}
{!! \ModStart\ModStart::js('asset/vendor/swiper/swiper.js') !!}

@section('headAppend')
    @parent
    <style>
        .vip-list .item .item-active-show{ display:none; }
        .vip-list .item.active .item-active-show{ display:block; }
    </style>
@endsection

@section('bodyAppend')
    @parent
    <script>
        window.__data = {
            isLogin: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\Module\Member\Auth\MemberUser::isLogin()) !!},
            countDownSeconds: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(modstart_config('Member_VipCountDown',1800)) !!},
        };
    </script>
    <script src="@asset('vendor/Member/script/memberVip.js')"></script>
@endsection

@section('body')

    <div class="pb-page-member-vip" style="border-radius:0px;padding:20px;">
        <div class="top">
            <div>
                <div class="member-info tw-flex tw-items-center">
                    <div class="tw-w-12 tw-mr-3">
                        <div class="ub-cover-1-1 tw-w-12 tw-rounded-full"
                             style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($_memberUser?$_memberUser['avatar']:'asset/image/avatar.svg')}});"></div>
                    </div>
                    <div class="tw-mr-3">
                        <div class="">
                            {{$_memberUser?\Module\Member\Util\MemberUtil::viewName($_memberUser):'未登录'}}
                        </div>
                        <div class="tw-mt-1">
                            @if(empty($_memberUser))
                                <a href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])}}"
                                   class="vip-button">
                                    注册/登录
                                </a>
                            @else
                                <a href="{{modstart_web_url('member')}}"
                                   class="vip-button">
                                    用户中心
                                </a>
                                @if(!empty(\Module\Member\Auth\MemberVip::get()))
                                    <div class="tw-inline-block tw-ml-3">
                                        您当前是
                                        <span class="vip-text ub-text-bold">
                                        {{\Module\Member\Auth\MemberVip::get('title')}}
                                    </span>
                                        @if(!\Module\Member\Auth\MemberVip::isDefault())
                                            ，
                                            过期时间为：{{\Module\Member\Auth\MemberUser::get('vipExpire')}}
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="tw-text-right tw-flex-grow">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <div class="body">
            <div class="pb-member-vip" style="padding-top:10px;">
                <form action="{{$__msRoot}}api/member_vip/buy" method="post" data-ajax-form>
                    <div class="vip-list-container-box">
                        <div class="vip-list-container">
                            <div class="vip-list vip-bg tw-rounded">
                                @foreach($memberVips as $memberVip)
                                    @if(!$memberVip['isDefault'])
                                        <div class="item tw-relative" data-vip-id="{{$memberVip['id']}}" style="padding:1rem 5px;">
                                            <div class="tw-text-xl tw-font-bold margin-bottom tw-pt-2">
                                                {{$memberVip['title']}}
                                            </div>
                                            <div class="margin-bottom">
                                                <div class="tw-text-lg tw-font-bold">
                                                    ￥{{$memberVip['price']}}
                                                    <span class="ub-text-xs">元</span>
                                                </div>
                                                <div class="tw-line-through tw-text-gray-500 ub-text-sm">
                                                    ￥{{$memberVip['priceMarket']}}
                                                </div>
                                            </div>
                                            <div>
                                                {{$memberVip['desc']?$memberVip['desc']:'[会员简要说明]'}}
                                            </div>
                                            @if($memberVip['priceMarket']>$memberVip['price'])
                                                <div class="item-active-show tw-absolute tw-left-0 tw-top-0 tw-p-1 tw-text-sm tw-bg-red-500 tw-rounded-tl-lg tw-rounded-br-lg tw-text-white">
                                                    限时立减 {{bcsub($memberVip['priceMarket'],$memberVip['price'],2)}}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <a href="javascript:;" class="nav left">
                                <i class="iconfont icon-angle-left"></i>
                            </a>
                            <a href="javascript:;" class="nav right">
                                <i class="iconfont icon-angle-right"></i>
                            </a>
                        </div>
                    </div>
                    <input type="hidden" name="vipId" value="0"/>
                    <input type="hidden" name="redirect" value="{{\ModStart\Core\Input\Request::currentPageUrl()}}"/>
                    <div class="lg:tw-text-left tw-text-center margin-top lg:tw-px-12 tw-py-4 vip-bg tw-rounded margin-bottom tw-flex-col lg:tw-flex-row tw-flex tw-items-center">
                        <div class="tw-flex-grow">
                            @if(\Module\Member\Auth\MemberUser::isLogin())
                                @if(\Module\PayCenter\Util\PayUtil::preferShowQuick())
                                    <div class="tw-text-left lg:tw-mr-4 tw-mb-4 lg:tw-mb-0">
                                        <div class="pay-price tw-hidden margin-bottom tw-float-right tw-pt-2" data-vip-info>
                                            <span class="ub-text-warning" data-vip-value data-vip-type>-</span>
                                            需要支付
                                            <span class="ub-text-warning">￥</span>
                                            <span class="ub-text-warning" data-vip-value data-vip-price>-</span>
                                            购买后 <span class="ub-text-warning" data-vip-value data-vip-expire>-</span> 过期
                                        </div>
                                        @include('module::PayCenter.View.inc.quick')
                                    </div>
                                    <script>
                                        window.__refreshMemberVipPay = function () {
                                            var vipId = parseInt($('[name="vipId"]').val());
                                            if (vipId > 0) {
                                                window.__payCenterQuick.prepareLazy(
                                                    '{{\Module\Member\Core\MemberVipPayCenterBiz::NAME}}',
                                                    {money: $('[data-vip-price]').text()},
                                                    {vipId: vipId}
                                                );
                                            } else {
                                                window.__payCenterQuick.empty();
                                            }
                                        };
                                        $(function () {
                                            window.__refreshMemberVipPay();
                                        });
                                    </script>
                                @else
                                    <div class="pay-price tw-hidden margin-bottom" data-vip-info>
                                        <span class="ub-text-warning" data-vip-value data-vip-type>-</span>
                                        需要支付
                                        <span class="ub-text-warning">￥</span>
                                        <span class="ub-text-warning" data-vip-value data-vip-price>-</span>
                                        购买后 <span class="ub-text-warning" data-vip-value data-vip-expire>-</span> 过期
                                    </div>
                                    <div class="pay-submit margin-bottom">
                                        <button type="submit" class="lg vip-button" data-pay-submit>
                                            <i class="iconfont icon-cart"></i>
                                            立即开通
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="tw-pb-3">
                                    <div class="margin-bottom">
                                        <a class="lg vip-button" href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])}}"
                                           target="_parent"
                                        >
                                            <i class="iconfont icon-cart"></i>
                                            登录后开通
                                        </a>
                                    </div>
                                    <div class="tw-text-lg vip-text tw-text-xl margin-bottom">
                                        开通VIP会员，享受更多特权
                                    </div>
                                </div>
                            @endif
                            <div class="margin-bottom ub-text-danger">
                                限时优惠剩余时间：<span data-count-down></span>
                            </div>
                        </div>
                        <div>
                            <div class="tw-py-1">
                                <div data-vip-open-list class="swiper tw-overflow-hidden tw-w-64 tw-h-48 tw--mb-3" style="overflow:hidden;">
                                    <div class="swiper-wrapper">
                                        @foreach(modstart_config('Member_VipOpenUsers',[]) as $u)
                                            <div class="swiper-slide">
                                                <div class="tw-flex tw-items-center tw-bg-white tw-rounded-full tw-px-3 tw-py-1">
                                                    <div class="tw-w-20 tw-text-left">{{mb_substr($u['name'],0,2)}}******</div>
                                                    <div class="tw-w-8 tw-text-yellow-400">{{$u['time']}}</div>
                                                    <div class="tw-w-32 tw-text-right">购买了 {{$u['title']}}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(!empty($memberVipRights))
                        <div class="vip-bg tw-py-3 lg:tw-px-10 tw-px-3 margin-bottom tw-rounded-lg" data-vip-right-list>
                            <div class="row">
                                @foreach($memberVipRights as $r)
                                    <div class="col-lg-2 col-md-3 col-6" style="display:none;" data-vip-right="{{join(',',$r['vipIds'])}}">
                                        <div class="tw-flex tw-items-center ub-text-sm">
                                            <div class="tw-pr-2">
                                                <img class="tw-w-8 tw-h-8 tw-object-cover tw-rounded-full" src="{{$r['image']}}" />
                                            </div>
                                            <div>
                                                <div class="vip-text">{{$r['title']}}</div>
                                                <div class="ub-text-muted">{{$r['desc']}}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="tw-rounded vip-bg vip-content-list tw-py-3 lg:tw-px-6">
                        @foreach($memberVips as $memberVip)
                            @if(!$memberVip['isDefault'])
                                <div class="item tw-hidden">
                                    <div class="tw-p-4 tw-text-lg vip-text">{{$memberVip['title']}}</div>
                                    <div class="tw-pb-4 tw-px-4 ">
                                        <div class="ub-html lg">
                                            {!! $memberVip['content'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="ub-panel margin-top" style="border-radius:0px;">
        <div class="head">
            <div class="title">
                <i class="iconfont icon-description ub-color-vip"></i>
                VIP开通说明
            </div>
        </div>
        <div class="body">
            <div class="ub-html lg">
                {!! modstart_config('Member_VipContent','VIP开通说明') !!}
            </div>
        </div>
    </div>

@endsection
