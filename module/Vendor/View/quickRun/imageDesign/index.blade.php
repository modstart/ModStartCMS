<div id="app" v-cloak></div>
<script>
    {!! \ModStart\Developer\LangUtil::langScriptPrepare([ "Select Local File" ]) !!};
    window.__selectorDialogServer = "{{$selectorDialogServer}}";
    window._data = {
        variables: {!! json_encode($variables) !!},
        imageConfig: {!! json_encode($imageConfig) !!}
    };
</script>

{{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
{{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
{{ \ModStart\ModStart::js('asset/entry/basic.js') }}
{{ \ModStart\ModStart::js('vendor/Vendor/entry/quickRunImageDesign.js') }}
