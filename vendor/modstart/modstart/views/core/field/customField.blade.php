<div class="line">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <div id="{{$id}}Input" class="tw-bg-white tw-rounded">
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            <table class="ub-table mini">
                <tr>
                    <td width="60">{{L('Type')}}</td>
                    <td>
                        <div style="padding-top:0.2rem;margin-bottom:-0.2rem;">
                            <el-radio v-model="value.type" label="">{{L('None')}}</el-radio>
                            <el-radio v-model="value.type" label="Text">{{L('Text')}}</el-radio>
                            <el-radio v-model="value.type" label="Radio">{{L('Radio')}}</el-radio>
                        </div>
                    </td>
                </tr>
                <tr v-if="value.type==='Radio' || value.type==='Text'">
                    <td width="60">{{L('Title')}}</td>
                    <td>
                        <input type="text" v-model="value.title" style="max-width:10rem;" />
                    </td>
                </tr>
                <tr v-if="value.type==='Radio'">
                    <td width="60">{{L('Option')}}</td>
                    <td>
                        <div v-for="(option,optionIndex) in value.data.option">
                            <input type="text" v-model="value.data.option[optionIndex]" style="max-width:10rem;" />
                            <a href="javascript:;" class="ub-text-danger" @click="value.data.option.splice(optionIndex,1)"><i class="iconfont icon-trash"></i></a>
                        </div>
                        <div>
                            <a href="javascript:;" class="ub-text-muted" @click="value.data.option.push('')"><i class="iconfont icon-plus"></i> {{L('Add')}}</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    {{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
    {{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
    {{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Input',
            data: {
                value: {!! json_encode($value) !!}
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            }
        });
    });
</script>
