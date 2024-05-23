@extends('modstart::admin.dialogFrame')

@section('pageTitle'){{L('Please Select')}}@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__data = {
            types: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($types) !!},
            links: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($links) !!}
        };
    </script>
    <script src="@asset('asset/entry/dialogLinkSelector.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection
