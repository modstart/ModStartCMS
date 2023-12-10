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
            record: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($record) !!},
            fieldNamePrefix: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($fieldNamePrefix) !!},
            CustomFieldType:{!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\ModStart\Core\Type\TypeUtil::dump(\Module\Vendor\QuickRun\CustomField\CustomFieldType::class)) !!}
        }
    </script>
    <script src="@asset('vendor/Vendor/entry/quickRunCustomFieldEdit.js')"></script>
@endsection
