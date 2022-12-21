@extends('modstart::admin.frame')

@section('pageTitle')模块授权@endsection

@section($_tabSectionName)
    <div class="ub-panel">
        <div class="head">
            <div class="title">
                <i class="iconfont icon-lock"></i>
                模块授权
            </div>
        </div>
        <div class="body" data-module-auth>
            <div class="ub-empty tw-my-20">
                <div class="icon">
                    <div class="iconfont icon-refresh tw-animate-spin"></div>
                </div>
                <div class="text">正在获取...</div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // 请勿删除，用于获取最新的安全通告（比如框架、模块有重大缺陷的应急通知等），请使用正版授权
        $(function(){
            $('body').append('<script src="https://modstart.com/api/modstart/auth?modules={{urlencode(join(',',$modules))}}"><' + '/script>');
        });
    </script>
@endsection

@section('adminPageMenu')
    <a href="{{modstart_admin_url('')}}">
        系统概况
    </a>
    <a href="javascript:;" class="active">
        模块授权
    </a>
@endsection
