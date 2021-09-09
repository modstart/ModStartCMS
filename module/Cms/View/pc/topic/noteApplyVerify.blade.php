@extends($_viewFrameDialog)

@section('pageTitle')投稿审核@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__data = {
            topicAlias: {!! json_encode(\Illuminate\Support\Facades\Input::get('topicAlias')) !!}
        };
    </script>
    <script src="@asset('vendor/Cms/entry/topicNoteApplyVerify.js')"></script>
@endsection

@section('bodyContent')
    <div id="app"></div>
@endsection





