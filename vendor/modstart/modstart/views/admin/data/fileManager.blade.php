@extends('modstart::admin.dialogFrame')

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__fileManager = {
            url: "{{\ModStart\Core\Input\Request::currentPageUrlWithOutQueries()}}".replace(/\/\w+$/, ''),
            category: '{{$category}}',
            permission: {
                'View':{!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\ModStart\Admin\Auth\AdminPermission::permit('DataFileManagerView')) !!},
                'Upload':{!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\ModStart\Admin\Auth\AdminPermission::permit('DataFileManagerUpload')) !!},
                'Delete':{!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\ModStart\Admin\Auth\AdminPermission::permit('DataFileManagerDelete')) !!},
                'Add/Edit':{!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\ModStart\Admin\Auth\AdminPermission::permit('DataFileManagerAdd/Edit')) !!}
            }
        };
    </script>
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('UploadScript',['source'=>'fileManager','server'=>\ModStart\Core\Input\Request::currentPageUrlWithOutQueries(),'id'=>$category,]); !!}
    <script src="@asset('asset/entry/dataFileManager.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection
