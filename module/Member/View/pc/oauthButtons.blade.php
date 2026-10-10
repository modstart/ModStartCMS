@if(\Module\Member\Config\MemberOauth::hasItems())
    <?php
    // 超过一定数量后折叠为“…”，点击展开（Laravel 5.1 Blade 不支持 @php，使用原生 PHP 标签）
    $oauthItems = [];
    foreach (\Module\Member\Config\MemberOauth::get() as $oauth) {
        if ($oauth->isSupport()) {
            $oauthItems[] = $oauth;
        }
    }
    $oauthMaxShow = 5;
    $oauthHasMore = count($oauthItems) > $oauthMaxShow;
    ?>
    <div class="oauth">
        <div>
            @if($oauthHasMore)
                @foreach(array_slice($oauthItems, 0, $oauthMaxShow) as $oauth)
                    {!! $oauth->render() !!}
                @endforeach
                <a href="javascript:;" class="oauth-more" title="展开更多登录方式"
                   onclick="$(this).closest('.oauth').toggleClass('expanded');return false;">
                    <i class="iconfont icon-ellipsis oauth-more-expand"></i>
                    <i class="iconfont icon-angle-up oauth-more-collapse"></i>
                </a>
                <div class="oauth-rest-line">
                    <span class="oauth-rest">
                        @foreach(array_slice($oauthItems, $oauthMaxShow) as $oauth)
                            {!! $oauth->render() !!}
                        @endforeach
                    </span>
                </div>
            @else
                @foreach($oauthItems as $oauth)
                    {!! $oauth->render() !!}
                @endforeach
            @endif
        </div>
    </div>
@endif
