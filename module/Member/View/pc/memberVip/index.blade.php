@extends($_viewFrame)

@section('pageTitleMain')开通VIP会员@endsection
@section('pageKeywords')开通VIP会员@endsection
@section('pageDescription')开通VIP会员@endsection

{!! \ModStart\ModStart::css('vendor/Member/style/member.css') !!}
@section('bodyAppend')
    @parent
    <script>
        $(function () {
            var $container = $('.vip-list-container');
            var $items = $('.pb-member-vip .vip-list .item');
            var $contents = $('.vip-content-list .item');
            $items.on('click', function () {
                var vipId = $(this).attr('data-vip-id');
                var index = $items.index($(this));
                $contents.hide().eq(index).show();
                $('[data-vip-info]').find('[data-vip-value]').html('-')
                $('[data-vip-info]').show();
                $items.removeClass('active');
                $(this).addClass('active');
                $('[name=vipId]').val($(this).attr('data-vip-id'));
                var $rights = $('[data-vip-right-list] [data-vip-right]');
                $rights.hide().filter(function(i,o){
                    return $(o).attr('data-vip-right').split(',').indexOf(vipId)>=0;
                }).show();
                @if(\Module\Member\Auth\MemberUser::isLogin())
                    MS.api.post(window.__msRoot + 'api/member_vip/calc', {vipId: vipId}, function (res) {
                        $('[data-vip-type]').html(res.data.type);
                        $('[data-vip-price]').html(res.data.price);
                        $('[data-vip-expire]').html(res.data.expire);
                        $('[data-vip-info]').show();
                        window.__refreshMemberVipPay && window.__refreshMemberVipPay();
                    });
                @endif
            });
            $container.find('.nav').on('click', function () {
                var currentItemIndex = $items.index($items.filter('.active'));
                if ($(this).hasClass('left')) {
                    currentItemIndex--;
                } else {
                    currentItemIndex++;
                }
                currentItemIndex = Math.max(0, Math.min(currentItemIndex, $items.length - 1));
                var $current = $items.eq(currentItemIndex).click();
                try {
                    $current.get(0).scrollIntoView({
                        behavior: 'smooth', block: 'nearest', inline: 'start'
                    });
                } catch (e) {
                }
                return false;
            });
            $($items.get(0)).click();
        });
    </script>
@endsection

@section('bodyContent')

    <div class="ub-container lg:tw-mt-4 tw-mt-1">

        <div class="pb-page-member-vip">
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
                <div class="pb-member-vip">
                    <form action="{{$__msRoot}}api/member_vip/buy" method="post" data-ajax-form>
                        <div class="vip-list-container-box margin-bottom">
                            <div class="vip-list-container">
                                <div class="vip-list vip-bg tw-rounded">
                                    @foreach($memberVips as $memberVip)
                                        @if(!$memberVip['isDefault'] && $memberVip['visible'])
                                            <div class="item" data-vip-id="{{$memberVip['id']}}">
                                                <div class="tw-text-xl tw-font-bold tw-py-4">
                                                    {{$memberVip['title']}}
                                                </div>
                                                <div class="tw-text-lg tw-font-bold tw-py-4">
                                                    ￥{{$memberVip['price']}}
                                                </div>
                                                <div>
                                                    {{$memberVip['desc']?$memberVip['desc']:'[会员简要说明]'}}
                                                </div>
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
                        <div class="ub-text-center tw-py-4 vip-bg tw-rounded margin-bottom">
                            @if(\Module\Member\Auth\MemberUser::isLogin())
                                <input type="hidden" name="vipId" value="0"/>
                                <input type="hidden" name="redirect" value="{{\ModStart\Core\Input\Request::currentPageUrl()}}"/>
                                @if(\Module\PayCenter\Util\PayUtil::preferShowQuick())
                                    <div class="tw-px-3 tw-mx-auto tw-text-left" style="max-width:780px;">
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
                                    <div class="pay-submit tw-pt-4">
                                        <button type="submit" class="lg vip-button" data-pay-submit>
                                            <i class="iconfont icon-cart"></i>
                                            立即开通
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="">
                                    <a class="lg vip-button" href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])}}">
                                        <i class="iconfont icon-cart"></i>
                                        登录后开通
                                    </a>
                                </div>
                            @endif
                        </div>
                        @if(!empty($memberVipRights))
                            <div class="vip-bg tw-p-3 margin-bottom tw-rounded-lg" data-vip-right-list>
                                <div class="row">
                                    @foreach($memberVipRights as $r)
                                        <div class="col-md-2 col-6" style="display:none;" data-vip-right="{{join(',',$r['vipIds'])}}">
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
                        <div class="tw-rounded vip-bg vip-content-list">
                            @foreach($memberVips as $memberVip)
                                @if(!$memberVip['isDefault'] && $memberVip['visible'])
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

        <div class="ub-panel margin-top">
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

        @if(modstart_module_enabled('MemberOrderCard'))
            @include('module::MemberOrderCard.View.inc.memberOrderCard.form')
        @endif

    </div>


@endsection
