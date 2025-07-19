<footer class="ub-footer-link reverse">
    <div class="ub-container">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-6">
                        <div class="link">
                            <div class="title">
                                关于
                            </div>
                            <div class="list">
                                @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('foot') as $nav)
                                    <a href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue(empty($nav['openType'])?null:$nav['openType'])}}>{{$nav['name']}}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="link">
                            <div class="title">
                                导航
                            </div>
                            <div class="list">
                                @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('footSecondary') as $nav)
                                    <a href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue(empty($nav['openType'])?null:$nav['openType'])}}>{{$nav['name']}}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="link">
                    <div class="title">
                        {{modstart_config('Cms_CompanyName','[公司名称]')}}
                    </div>
                    <div class="list tw-pt-4 tw-text-gray-400">
                        <div class="tw-py-1">
                            <i class="iconfont icon-email tw-inline-block tw-w-4"></i>
                            <span class="tw-inline-block">{{modstart_config('Site_ContactEmail','[公司邮箱]')}}</span>
                        </div>
                        <div class="tw-py-1">
                            <i class="iconfont icon-phone tw-inline-block tw-w-4"></i>
                            <span class="tw-inline-block">{{modstart_config('Site_ContactPhone','[公司电话]')}}</span>
                        </div>
                        <div class="tw-py-1">
                            <i class="iconfont icon-address tw-inline-block tw-w-4"></i>
                            <span class="tw-inline-block">{{modstart_config('Site_ContactAddress','[公司地址]')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tw-p-4 tw-text-center">
        <a href="http://beian.miit.gov.cn" target="_blank" class="tw-text-gray-100 hover:tw-text-gray-100">
            {{modstart_config('siteBeian','[网站备案信息]')}}
        </a>
        @if(modstart_config('siteBeianGonganText'))
            <a href="{{modstart_config('siteBeianGonganLink')}}" target="_blank" class="tw-text-gray-100 hover:tw-text-gray-100">
                 <img alt="{{modstart_config('siteBeianGonganText')}}" src="@asset('vendor/Site/image/gongan.png')" />
                {{modstart_config('siteBeianGonganText')}}
            </a>
        @endif
        &copy;{{modstart_config('siteDomain','[网站域名]')}}
    </div>
</footer>
