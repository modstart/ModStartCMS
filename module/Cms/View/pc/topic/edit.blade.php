@extends($_viewFrame)

@section('pageTitleMain'){{\ModStart\Core\Util\CRUDUtil::id()?'编辑专题':'创建专题'}}@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__selectorDialogServer = "{{modstart_web_url('member_data/file_manager')}}";
        window.__data = {
            id:{{\ModStart\Core\Util\CRUDUtil::id()}}
        };
    </script>
    <script src="@asset('vendor/Cms/entry/topicEdit.js')"></script>
@endsection

@section('bodyContent')
    <div class="ub-container">
        <div class="row">
            <div class="col-md-9">
                <div id="app"></div>
            </div>
            <div class="col-md-3">
                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">专题说明</div>
                    </div>
                    <div class="body">
                        <div class="ub-html">
                            {!! modstart_config('moduleCmsTopicEditTip') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection





