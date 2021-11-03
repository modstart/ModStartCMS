@extends($_viewFrameDialog)

@section('pageTitle')收录到专题@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__data = {
            noteAlias: {!! json_encode(\Illuminate\Support\Facades\Input::get('noteAlias')) !!}
        };
    </script>
    <script src="@asset('vendor/CmsWriter/entry/noteAddToTopic.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection





