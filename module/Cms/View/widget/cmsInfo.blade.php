<div class="ub-panel">
    <div class="head">
        <div class="title">
            <i class="iconfont icon-page"></i>
            系统信息
        </div>
    </div>
    <div class="body">
        <div class="tw-leading-loose">
            <div>
                当前正在使用
                <b>ModStartCMS</b>
                <b>V{{\App\Constant\AppConstant::VERSION}}</b>
            </div>
            @if(!config('modstart.admin.versionCheckDisable',false))
                <div data-admin-version></div>
            @endif
        </div>
        <script type="text/javascript">
            // 最新版本检测
            $('body').append('<script src="https://modstart.com/api/app/version_check?app={{\App\Constant\AppConstant::APP}}&version={{\App\Constant\AppConstant::VERSION}}"><' + '/script>');
        </script>
    </div>
</div>
