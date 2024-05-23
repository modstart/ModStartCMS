<div class="line" data-field="{{$name}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i
                    class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Input">
            <input type="hidden" name="{{$name}}" :value="jsonValue"/>
            <table class="ub-table border-all tw-bg-white">
                <thead>
                <tr>
                    <th width="100">标题</th>
                    <th width="100">
                        标识
                        <a href="javascript:;" data-tip-popover="字母数字下划线，不能重复，通常为字段标题的英文或拼音" class="ub-text-muted">
                            <i class="iconfont icon-warning"></i>
                        </a>
                    </th>
                    <th width="150">类型</th>
                    <th>参数</th>
                    <th width="150">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(v,vIndex) in value">
                    <td>
                        <el-input v-model="value[vIndex].title" plaleholder="中文提示"/>
                    </td>
                    <td>
                        <el-input v-model="value[vIndex].name" placeholder="a-zA-Z0-9"/>
                    </td>
                    <td>
                        <el-select v-model="value[vIndex].type" placeholder="请选择">
                            @foreach(\ModStart\Field\Type\DynamicFieldsType::getList() as $k=>$v)
                                @if(null===$enabledFieldTypes||in_array($k,$enabledFieldTypes))
                                    <el-option label="{{$v}}" value="{{$k}}"></el-option>
                                @endif
                            @endforeach
                        </el-select>
                    </td>
                    <td>
                        <div v-if="value[vIndex].type==='text'">
                            <div>
                                <el-input v-model="value[vIndex].defaultValue">
                                    <template slot="prepend">默认值</template>
                                </el-input>
                            </div>
                        </div>
                        <div v-else-if="value[vIndex].type==='textarea'">
                            <div>
                                <el-input v-model="value[vIndex].defaultValue">
                                    <template slot="prepend">默认值</template>
                                </el-input>
                            </div>
                        </div>
                        <div v-else-if="value[vIndex].type==='number'">
                            <div>
                                <el-input-number v-model="value[vIndex].defaultValue">
                                    <template slot="prepend">默认值</template>
                                </el-input-number>
                            </div>
                        </div>
                        <div v-else-if="value[vIndex].type==='switch'">
                            <div>
                                <el-radio v-model="value[vIndex].defaultValue" :label="false">
                                    <span class="tw-text-sm">
                                        默认不选中
                                    </span>
                                </el-radio>
                                <el-radio v-model="value[vIndex].defaultValue" :label="true">
                                    <span class="tw-text-sm">
                                        默认选中
                                    </span>
                                </el-radio>
                            </div>
                        </div>
                        <div v-else-if="['file','files'].includes(value[vIndex].type)">
                            <div>
                                <el-checkbox v-model="value[vIndex].data.switch1">
                                    <span class="tw-text-sm">不使用文件管理器</span>
                                </el-checkbox>
                            </div>
                            <div>
                                <el-input v-model="value[vIndex].data.text1">
                                    <template slot="prepend">上传按钮文字</template>
                                </el-input>
                            </div>
                        </div>
                        <div v-else-if="['radio','select','checkbox'].includes(value[vIndex].type)">
                            <div>
                                <table class="ub-table mini border">
                                    <thead>
                                    <tr>
                                        <th>选项</th>
                                        <th width="100">默认</th>
                                        <th width="50">&nbsp</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(o,oIndex) in value[vIndex].data.options">
                                        <td>
                                            <el-input v-model="value[vIndex].data.options[oIndex].title"/>
                                        </td>
                                        <td>
                                            <el-switch v-model="value[vIndex].data.options[oIndex].active"/>
                                        </td>
                                        <td class="ub-text-right" width="80">
                                            <a href="javascript:;" class="ub-text-muted" v-if="oIndex>0" @click="up(value[vIndex].data.options,oIndex)"><i class="iconfont icon-direction-up"></i></a>
                                            <a href="javascript:;" class="ub-text-muted" v-if="oIndex<value[vIndex].data.options.length-1" @click="down(value[vIndex].data.options,oIndex)"><i class="iconfont icon-direction-down"></i></a>
                                            <a href="javascript:;" class="ub-text-muted"
                                               @click="value[vIndex].data.options.splice(oIndex,1)">
                                                <i class="iconfont icon-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <a href="javascript:;" class="ub-text-muted"
                                               @click="value[vIndex].data.options.push({title:'',active:false})">
                                                <i class="iconfont icon-plus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tw-mt-2">
                            <el-input placeholder="" v-model="value[vIndex].placeholder">
                                <template slot="prepend">提示</template>
                            </el-input>
                        </div>
                        <div class="tw-mt-2">
                            <el-checkbox v-model="value[vIndex].isRequired">
                                <span class="tw-text-sm">必填</span>
                            </el-checkbox>
                        </div>
                    </td>
                    <td class="tw-text-right">
                        <a href="javascript:;" class="btn btn-sm btn-round" @click="up(value,vIndex)" v-if="vIndex>0">
                            <i class="iconfont icon-direction-up"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-sm btn-round" @click="down(value,vIndex)" v-if="vIndex<value.length-1">
                            <i class="iconfont icon-direction-down"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-sm btn-round ub-text-danger" @click="value.splice(vIndex,1)">
                            <i class="iconfont icon-trash"></i>
                        </a>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <a href="javascript:;" class="ub-text-muted" @click="doValueAdd">
                            <i class="iconfont icon-plus"></i>
                            {{L('Add')}}
                        </a>
                    </td>
                </tr>
                </tfoot>
            </table>
            {{--            <pre>@{{JSON.parse(jsonValue,null,2)}}</pre>--}}
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
{{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
{{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
{{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
{{ \ModStart\ModStart::js('asset/entry/basic.js') }}
<script>
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Input',
            data: {
                value: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(null===$value?(null===$defaultValue?[]:$defaultValue):$value) !!},
            },
            computed: {
                jsonValue: function () {
                    // fire change event
                    setTimeout(function () {
                        $('[name={{$name}}]').trigger('change');
                    }, 0);
                    return JSON.stringify(this.value);
                }
            },
            methods: {
                up:MS.collection.sort.up,
                down:MS.collection.sort.down,
                doValueAdd() {
                    this.value.push({
                        name: '',
                        title: '',
                        type: 'text',
                        data: {
                            options: [],
                            switch1: false,
                            switch2: false,
                            text1: '',
                            text2: '',
                        },
                        isRequired: false,
                        placeholder: '',
                        defaultValue: null
                    })
                }
            }
        });
    });
</script>
