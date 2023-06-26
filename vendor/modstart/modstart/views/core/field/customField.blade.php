<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Input" class="tw-bg-white tw-rounded">
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            <table class="ub-table mini">
                <tr>
                    <td width="60">
                        {{L('Type')}}
                    </td>
                    <td>
                        <div>
                            <el-select v-model="value.type" size="mini" style="width:auto;">
                                <el-option label="{{L('None')}}" value=""></el-option>
                                <el-option label="{{L('Text')}}" value="Text"></el-option>
                                <el-option label="{{L('Radio')}}" value="Radio"></el-option>
                                <el-option label="{{L('File')}}" value="File"></el-option>
                                <el-option label="{{L('Files')}}" value="Files"></el-option>
                            </el-select>
                        </div>
                    </td>
                </tr>
                <tr v-if="!!value.type">
                    <td width="60">
                        {{L('Title')}}
                    </td>
                    <td>
                        <input type="text" v-model="value.title" />
                    </td>
                </tr>
                <tr v-if="value.type==='Radio'">
                    <td width="60">
                        {{L('Option')}}
                    </td>
                    <td>
                        <div v-for="(option,optionIndex) in value.data.option" class="tw-flex">
                            <div class="tw-flex-grow">
                                <input type="text" v-model="value.data.option[optionIndex]" />
                            </div>
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
                value: {!! json_encode(null===$value?(null===$defaultValue?[
                            'type' => '',
                            'title' => '',
                            'data' => [
                                'option' => [],
                            ],
                        ]:$defaultValue):$value) !!}
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            }
        });
    });
</script>
