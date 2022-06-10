@extends('module::Vendor.View.pc.dialogFrame')

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
            "Reverse Select Order",
        ]) !!}
    </script>
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
    <script src="@asset('asset/entry/dataFileManager.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection
