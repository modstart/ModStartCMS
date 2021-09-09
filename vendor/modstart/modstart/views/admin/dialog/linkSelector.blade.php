@extends('modstart::admin.dialogFrame')

@section('pageTitle',L('Please Select'))

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__data = {
            links: {!! json_encode($links) !!}
        };
    </script>
    <script src="@asset('asset/entry/dialogLinkSelector.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection