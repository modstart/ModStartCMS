<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        <div id="{{$id}}Input" class="tw-bg-white tw-rounded tw-p-4">
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            <el-table
                    :data="records" size="mini"
                    style="width:100%;margin:0;border-radius:3px;">
                <el-table-column width="100" label="{{L('ID')}}">
                    <template slot-scope="scope">
                        @{{scope.row.id}}
                    </template>
                </el-table-column>
                @if($itemStyle=='coverTitle')
                <el-table-column label="{{L('Cover')}}">
                    <template slot-scope="scope">
                        <div class="ub-cover-1-1 tw-w-10 tw-rounded tw-bg-gray-100" :style="{backgroundImage:'url('+scope.row.cover+')'}"></div>
                    </template>
                </el-table-column>
                @endif
                <el-table-column label="{{L('Title')}}">
                    <template slot-scope="scope">
                        @{{scope.row.title}}
                    </template>
                </el-table-column>
                <el-table-column width="50"  align="center">
                    <template slot-scope="scope">
                        <a href="javascript:;" class="ub-text-danger" @click="records.splice(scope.$index,1)"><i class="iconfont icon-trash"></i></a>
                    </template>
                </el-table-column>
            </el-table>
            <a href="javascript:;" class="ub-text-muted tw-block tw-text-center" @click="doSelect"><i class="iconfont icon-plus"></i> {{L('Add')}}</a>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
{{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
{{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
{{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
<script>
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Input',
            data: {
                value: {!! json_encode($value?$value:[]) !!},
                records: [],
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.records.map(o=>o.id));
                }
            },
            mounted:function(){
                @if(!empty($value))
                    this.doPreview({!! json_encode($value) !!});
                @endif
            },
            methods:{
                doPreview: function(ids, isAppend){
                    if (!ids.length) return;
                    isAppend = isAppend || false;
                    var me = this;
                    MS.dialog.loadingOn();
                    MS.api.post("{{$previewUrl}}",{ids:ids.join(',')},function(res){
                        MS.dialog.loadingOff();
                        MS.api.defaultCallback(res,{
                            success:function(res){
                                if(isAppend){
                                    me.records = me.records.concat(res.data);
                                }else{
                                    me.records = res.data;
                                }
                            }
                        })
                    });
                },
                doSelect: function(){
                    var me = this;
                    window.__dialogSelectIds = []
                    MS.dialog.dialog("{{$selectUrl}}", {
                        width: '90%',
                        height: '90%',
                        closeCallback: function() {
                            me.doPreview(window.__dialogSelectIds,true);
                        }
                    })
                },
            }
        });
    });
</script>
