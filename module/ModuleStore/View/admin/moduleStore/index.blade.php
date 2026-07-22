@extends('modstart::admin.frame')

@section('pageTitle')模块管理@endsection

@section('headAppend')
    @parent
    <script>
        var __grow = __grow || [];
        (function() {
            var g = document.createElement("script");
            g.src = "https://g.tecmz.com/grow/page.js?modstart";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(g, s);
        })();
        $(function () {
            $(document).on('click', '[data-tk-event]', function () {
                var pcs = $(this).attr('data-tk-event').split(',')
                if(__grow.trackEvent){
                    __grow.trackEvent(pcs[0]+'.'+pcs[1],{
                        module: pcs[2] || ''
                    });
                }
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

