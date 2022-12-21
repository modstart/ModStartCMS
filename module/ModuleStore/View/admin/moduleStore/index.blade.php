@extends('modstart::admin.frame')

@section('pageTitle')模块管理@endsection

@section('headAppend')
    @parent
    <script>
        var _hmt = _hmt || [];
        (function () {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?e3403438a2b250ca086ee44c2c5ebf72";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
        $(function () {
            $(document).on('click', '[data-tk-event]', function () {
                var pcs = $(this).attr('data-tk-event').split(',')
                _hmt && _hmt.push(['_trackEvent', pcs[0], pcs[1], pcs[2] || '', pcs[3] || '']);
            });
        });
    </script>
@endsection

@section($_tabSectionName)
    <div id="app"></div>
@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window.__data = {
            apiBase: '{{\Module\ModuleStore\Util\ModuleStoreUtil::REMOTE_BASE}}',
            modstartParam: {
                version: '{{\ModStart\ModStart::$version}}',
                url: window.location.href
            }
        };
    </script>
    <script src="@asset('vendor/ModuleStore/entry/moduleStore.js')"></script>
@endsection

