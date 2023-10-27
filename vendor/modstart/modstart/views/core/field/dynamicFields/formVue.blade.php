{!! \ModStart\ModStart::js(['asset/vendor/vue.js','asset/vendor/element-ui/index.js','vendor/Vendor/entry/all.js']) !!}
<?php $id = \ModStart\Core\Util\IdUtil::generate('DynamicFields'); ?>
<div id="{{$id}}" v-cloak>
    {!! \ModStart\Field\DynamicFields::renderAllFormFieldVue($fields,['modelPrefix'=>'value']) !!}
    <input type="hidden" name="{{$param['name']}}" :value="jsonValue" />
</div>
<script>
    {!! \ModStart\Developer\LangUtil::langScriptPrepare([ "Select Local File" ]) !!}
    window.__selectorDialogServer = "{{modstart_web_url('member_data/file_manager')}}";
    $(function () {
        new Vue({
            el: '#{{$id}}',
            data() {
                return {
                    value: {!! \ModStart\Core\Util\SerializeUtil::jsonEncodeObject($fieldsData) !!}
                };
            },
            computed: {
                jsonValue() {
                    return JSON.stringify(this.value);
                }
            }
        });
    });
</script>
