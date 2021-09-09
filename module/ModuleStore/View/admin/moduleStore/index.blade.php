@extends('modstart::admin.frame')

@section('pageTitle')模块管理@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__data = {};
    </script>
    <script src="@asset('vendor/ModuleStore/entry/moduleStore.js')"></script>
@endsection

