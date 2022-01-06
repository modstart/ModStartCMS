@extends('modstart::admin.dialogFrame')

@section('pageTitle')字段管理@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__data = {
            record: {!! json_encode($record) !!},
            ModelFieldType:{!! json_encode(\ModStart\Core\Type\TypeUtil::dump(\Module\Cms\Type\CmsModelFieldType::class)) !!}
        }
    </script>
    <script src="@asset('vendor/Cms/entry/adminModelFieldEdit.js')"></script>
@endsection
