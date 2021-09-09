@if(\Module\Vendor\Oauth\OauthUtil::hasOauth())
    <div class="oauth">
        <div class="title">
            您还可以使用以下方式登录
        </div>
        <div class="body">
            @if(\Module\Vendor\Oauth\OauthUtil::isWechatMobileEnable() && \ModStart\Core\Util\AgentUtil::isWechat())
                <a href="{{$__msRoot}}oauth_login_{{\Module\Vendor\Oauth\OauthType::WECHAT_MOBILE}}?redirect={{urlencode($redirect)}}" class="wechat"><i class="iconfont icon-wechat"></i></a>
            @endif
            @if(\Module\Vendor\Oauth\OauthUtil::isWechatEnable() && \ModStart\Core\Util\AgentUtil::isPC())
                <a target="_blank" href="{{$__msRoot}}oauth_login_{{\Module\Vendor\Oauth\OauthType::WECHAT}}?redirect={{urlencode($redirect)}}" class="wechat"><i class="iconfont icon-wechat"></i></a>
            @endif
            @if(\Module\Vendor\Oauth\OauthUtil::isQQEnable())
                <a href="{{$__msRoot}}oauth_login_{{\Module\Vendor\Oauth\OauthType::QQ}}?redirect={{urlencode($redirect)}}"
                   class="qq"><i class="iconfont icon-qq"></i></a>
            @endif
            @if(\Module\Vendor\Oauth\OauthUtil::isWeiboEnable())
                <a href="{{$__msRoot}}oauth_login_{{\Module\Vendor\Oauth\OauthType::WEIBO}}?redirect={{urlencode($redirect)}}"
                   class="weibo"><i class="iconfont icon-weibo"></i></a>
            @endif
        </div>
    </div>
@endif
