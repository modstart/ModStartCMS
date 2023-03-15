<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        <div id="{{$id}}Input">
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            <el-table
                    :data="value" size="mini" border
                    style="width:100%;margin:0;border-radius:3px;">
                <el-table-column width="200" label="{{$keyTitle}}">
                    <template slot-scope="scope">
                        <input type="text" v-model="scope.row.{{$keyLabel}}" placeholder="{{$keyPlaceholder}}" />
                    </template>
                </el-table-column>
                <el-table-column label="{{$valueTitle}}">
                    <template slot-scope="scope">
                        <input type="text" v-model="scope.row.{{$valueLabel}}" placeholder="{{$valuePlaceholder}}" />
                    </template>
                </el-table-column>
                <el-table-column width="50"  align="center">
                    <template slot-scope="scope">
                        <a href="javascript:;" class="ub-text-danger" @click="value.splice(scope.$index,1)"><i class="iconfont icon-trash"></i></a>
                    </template>
                </el-table-column>
            </el-table>
            <a href="javascript:;" class="ub-text-muted" @click="value.push({ {{$keyLabel}} :'', {{$valueLabel}}:'' })"><i class="iconfont icon-plus"></i> {{L('Add')}}</a>
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
                value: {!! !empty($value)?json_encode($value):json_encode($defaultValue?$defaultValue:[]) !!}
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            }
        });
    });
</script>
