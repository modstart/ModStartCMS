@extends('module::Vendor.View.pc.dialogFrame')

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__fileManager = {
            url: "{{\ModStart\Core\Input\Request::currentPageUrlWithOutQueries()}}".replace(/\/\w+$/, ''),
            category: '{{$category}}',
            permission: {
                'View':true,
                'Upload':true,
                'Delete':true,
                'Add/Edit':true
            }
        };
    </script>
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('UploadScript',['source'=>'fileManager','server'=>\ModStart\Core\Input\Request::currentPageUrlWithOutQueries(),'id'=>$category,]); !!}
    <script src="@asset('asset/entry/dataFileManager.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection
