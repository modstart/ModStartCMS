@extends($_viewMemberFrame)

@section('pageTitleMain')编辑文章@endsection

@section('memberBodyContent')
    <div id="app"></div>
@endsection

@section('bodyAppend')
    <script src="@asset('asset/common/editor.js')"></script>
    <script src="@asset('asset/common/editorMarkdown.js')"></script>
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__selectorDialogServer = "{{modstart_web_url('member_data/file_manager')}}";
        window.__data = {
            id:{{\ModStart\Core\Util\CRUDUtil::id()}}
        };
    </script>
    <script src="@asset('vendor/CmsWriter/entry/writerPostEdit.js')"></script>
@endsection
