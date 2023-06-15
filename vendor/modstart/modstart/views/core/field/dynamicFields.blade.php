<div class="line">
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
            <table class="ub-table border-all head-dark">
                <thead>
                <tr>
                    <th width="100">标题</th>
                    <th width="100">
                        标识
                        <a href="javascript:;" data-tip-popover="字母数字下划线，作为字段的唯一标识" class="ub-text-muted">
                            <i class="iconfont icon-warning"></i>
                        </a>
                    </th>
                    <th width="100">类型</th>
                    <th>参数</th>
                    <th width="50">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(v,vIndex) in value">
                    <td>
                        <el-input v-model="value[vIndex].title" plaleholder="中文提示"/>
                    </td>
                    <td>
                        <el-input v-model="value[vIndex].name" placeholder="字母数字下划线"/>
                    </td>
                    <td>
                        <el-select v-model="value[vIndex].type" placeholder="请选择">
                            @foreach(\ModStart\Field\Type\DynamicFieldsType::getList() as $k=>$v)
                                <el-option label="{{$v}}" value="{{$k}}"></el-option>
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
                                        <td class="ub-text-center">
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
                            <el-checkbox v-model="value[vIndex].isRequired"><span class="tw-text-sm">必填</span>
                            </el-checkbox>
                        </div>
                    </td>
                    <td class="tw-text-center">
                        <a href="javascript:;" class="ub-text-danger" @click="value.splice(vIndex,1)">
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
                value: {!! $value?json_encode($value):($defaultValue?json_encode($defaultValue):'[]') !!},
            },
            computed: {
                jsonValue: function () {
                    return JSON.stringify(this.value);
                }
            },
            methods: {
                doValueAdd() {
                    this.value.push({
                        name: '',
                        title: '',
                        type: 'text',
                        data: {
                            options: []
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
