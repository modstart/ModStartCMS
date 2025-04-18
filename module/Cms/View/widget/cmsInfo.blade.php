<div class="row">
    <div class="col-md-6">
        <div class="ub-panel">
            <div class="head">
                <div class="title">
                    <i class="iconfont icon-file"></i>
                    系统信息
                </div>
            </div>
            <div class="body">
                <div style="min-height:5.5rem;">
                    <div class="tw-mb-2">
                        当前正在使用
                        <b>ModStartCMS</b>
                        <b>V{{\App\Constant\AppConstant::VERSION}}</b>
                    </div>
                    @if(!config('modstart.admin.versionCheckDisable',false))
                        <div data-admin-version class="tw-mb-2"></div>
                    @endif
                </div>
                <script type="text/javascript">
                    // 最新版本检测
                    $('body').append('<script src="https://modstart.com/api/app/version_check?app={{\App\Constant\AppConstant::APP}}&version={{\App\Constant\AppConstant::VERSION}}"><' + '/script>');
                </script>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ub-panel">
            <div class="head">
                <div class="title">
                    <i class="iconfont icon-corp"></i>
                    版权说明
                </div>
            </div>
            <div class="body">
                <div style="min-height:5.5rem;">
                    <div
                        class="tw-w-28 tw-pt-1 tw-mx-auto tw-mb-3 lg:tw--mt-8 lg:tw-float-right ub-border tw-rounded-lg tw-box-content">
                        <div class="tw-font-bold tw-text-center">
                            <i class="iconfont icon-customer"></i>
                            专属客服
                        </div>
                        <div class="tw-cursor-pointer" data-image-preview="https://modstart.com/code_dynamic/modstart_wx">
                            <img class="tw-w-28 tw-h-28 tw-rounded-lg"
                                 src="https://modstart.com/code_dynamic/modstart_wx"/>
                        </div>
                    </div>
                    <div class="tw-mb-2">
                        使用遇到问题请
                        <a href="javascript:;" data-dialog-title="工单反馈" data-dialog-height="95%"
                           data-dialog-request="https://modstart.com/feedback_ticket">
                            <i class="iconfont icon-description"></i>
                            提交工单
                        </a>
                        反馈给我们
                    </div>
                    <div class="tw-mb-2">
                        本软件基于 Apache 2.0 协议开源，支持免费商用
                    </div>
                    <div class="tw-mb-2">
                        在使用过程中请保留版权，如需定制请
                        <a href="https://www.tecmz.com/product/{{\App\Constant\AppConstant::APP}}"
                           target="_blank"><i class="iconfont icon-phone"></i> 联系我们</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
