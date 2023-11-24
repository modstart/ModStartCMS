@extends('modstart::admin.dialogFrame')

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        {!! \ModStart\Developer\LangUtil::langScriptPrepare([
            "Add Category",
            "Add Success",
            "Category",
            "Confirm",
            "Confirm Delete ?",
            "Copy Link",
            "Custom Link",
            "Delete",
            "Delete Category",
            "Delete Success",
            "Edit",
            "Edit Category",
            "Edit File",
            "Edit Success",
            "File(s)",
            "Filter",
            "File Gallery",
            "Loading",
            "Local Upload",
            "Name",
            "No Records",
            "Parent",
            "Please Input",
            "Please Select",
            "Select %d item(s) at most",
            "Select %d item(s) at least",
            "Url",
            "Select Local File",
            "Copy Success",
            "Copy Fail",
            "Image Gallery",
            "File Gallery",
            "Copy Links",
        ]) !!}
    </script>
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
    <script src="@asset('asset/entry/dataFileManager.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection
