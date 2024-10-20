<?php
$useVueRender = false;
if($field->selectSearch() || $field->selectRemote()){
    $useVueRender = true;
}
?>
<div class="field auto" data-grid-filter-field="{{$id}}" data-grid-filter-field-column="{{$column}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        @if($useVueRender)
            <div id="{{$id}}App" v-cloak class="tw-inline-block">
                <input type="hidden" id="{{$id}}Select" :value="value" />
                <el-select
                    v-model="value" size="small"
                    clearable
                    @if($field->selectSearch() || $field->selectRemote()) filterable @endif
                    @if($field->selectRemote()) remote :remote-method="onRemoteMethod" @endif
                    :loading="loading">
                    <el-option
                        v-for="item in optionRecords"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value"></el-option>
                </el-select>
            </div>
        @else
            <div class="layui-form tw-inline-block" lay-filter="{{$id}}">
                <select class="form" id="{{$id}}Select" lay-ignore>
                    @if(!empty($optionContainsAll))
                        <option value="" @if(null===$defaultValue) selected @endif>{{L('All')}}</option>
                    @endif
                    @foreach($field->options() as $k=>$v)
                        <option value="{{$k}}" @if(null!==$defaultValue&&$defaultValue==$k) selected @endif>{{$v}}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>
@if($useVueRender)
{{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
{{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
{{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
@endif
<script>
    $(function () {
        var $field = $('[data-grid-filter-field={{$id}}]');

        @if($useVueRender)
            var app = new Vue({
                el: '#{{$id}}App',
                data: {
                    loading: false,
                    value: '',
                    options:[],
                },
                computed:{
                    optionRecords(){
                        const options = [];
                        @if(!empty($optionContainsAll))
                            options.push({label:"{{L('All')}}",value:""});
                        @endif
                        return options.concat(this.options);
                    }
                },
                mounted(){
                    @foreach($field->options() as $k=>$v)
                        this.options.push({label:"{{$v}}",value:"{{$k}}"});
                    @endforeach
                    @if($field->selectRemote())
                        this.onRemoteMethod('');
                    @endif
                },
                methods:{
                    onRemoteMethod(keywords){
                        if(this.loading){
                            return;
                        }
                        this.loadding = true;
                        MS.api.post('{{$field->selectRemote()}}',{keywords:keywords},(res)=>{
                            this.loading = false;
                            MS.api.defaultCallback(res,{
                                success:(res)=>{
                                    this.options = res.data.options;
                                }
                            });
                        });
                    }
                }
            });
        @endif

        $field.data('get', function () {
            return {
                '{{$column}}': {
                    eq: $('#{{$id}}Select').val()
                }
            };
        });
        $field.data('reset', function () {
            @if($useVueRender)
                app.value = '';
            @else
                $('#{{$id}}Select').val('');
            @endif
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('eq' in data[i][k])) {
                        @if($useVueRender)
                            app.value = '';
                        @else
                            $('#{{$id}}Select').val(data[i][k].eq);
                        @endif
                    }
                }
            }
        });
    });
</script>
