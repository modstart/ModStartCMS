@extends('modstart::admin.frame')

@section('pageTitle')系统升级@endsection

@section($_tabSectionName)
    <div id="app"></div>
@endsection

@section('adminPageMenu')
    <a href="{{modstart_admin_url('')}}">
        系统概况
    </a>
    <a href="javascript:;" class="active">
        系统升级
    </a>
@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script><script>
        window.__data = {
            apiBase: '{{\Module\AdminManager\Util\UpgradeUtil::REMOTE_BASE}}',
            modstartParam: {
                version: '{{\ModStart\ModStart::$version}}',
                url: window.location.href
            }
        };
    </script>
    <script src="@asset('vendor/AdminManager/entry/upgrade.js')"></script>
@endsection
