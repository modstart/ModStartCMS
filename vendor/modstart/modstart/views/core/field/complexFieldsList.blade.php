<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-tertiary" href="javascript:;" data-tip-popover="{{$tip}}"><i
                    class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Input">
            <input type="hidden" name="{{$name}}" :value="jsonValue"/>
            <table class="ub-table border tw-bg-white">
                <thead>
                <tr>
                    @foreach($fields as $f)
                        <th>
                            {{empty($f['title'])?$f['name']:$f['title']}}
                            @if(!empty($f['tip']))
                                <a class="ub-text-tertiary" href="javascript:;" data-tip-popover="{{$f['tip']}}"><i class="iconfont icon-warning"></i></a>
                            @endif
                        </th>
                    @endforeach
                    @if(!empty($itemActions))
                        <th width="120">&nbsp;</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr v-for="(v,vIndex) in value">
                    @foreach($fields as $f)
                        <td>
                            @if($f['type']=='display')
                                {!! "{"."{ value[vIndex]['".$f['name']."'] }"."}" !!}
                            @elseif($f['type']=='switch')
                                <el-switch v-model="value[vIndex]['{{$f['name']}}']"></el-switch>
                            @elseif($f['type']=='text')
                                <el-input v-model="value[vIndex]['{{$f['name']}}']"
                                          placeholder="{{empty($f['placeholder'])?'':$f['placeholder']}}"
                                          size="mini"></el-input>
                            @elseif($f['type']=='textarea')
                                <el-input v-model="value[vIndex]['{{$f['name']}}']"
                                          placeholder="{{empty($f['placeholder'])?'':$f['placeholder']}}"
                                          type="textarea"
                                          size="mini"></el-input>
                            @elseif($f['type']=='icon')
                                <icon-input v-model="value[vIndex]['{{$f['name']}}']" :icons="iconsFilter"
                                            :inline="true"></icon-input>
                            @elseif($f['type']=='image')
                                <image-selector v-model="value[vIndex]['{{$f['name']}}']"></image-selector>
                            @elseif($f['type']=='values')
                                <values-editor v-model="value[vIndex]['{{$f['name']}}']"></values-editor>
                            @elseif($f['type']=='number')
                                <el-input-number v-model="value[vIndex]['{{$f['name']}}']"
                                                 placeholder="{{empty($f['placeholder'])?'':$f['placeholder']}}"
                                                 size="mini"></el-input-number>
                            @elseif($f['type']=='text-number')
                                <el-input v-model="value[vIndex]['{{$f['name']}}']"
                                          placeholder="{{empty($f['placeholder'])?'':$f['placeholder']}}"
                                          size="mini"></el-input>
                            @elseif($f['type']=='select')
                                <el-select v-model="value[vIndex]['{{$f['name']}}']"
                                           placeholder="{{empty($f['placeholder'])?'':$f['placeholder']}}">
                                    @foreach($f['option'] as $k=>$v)
                                        <el-option :key="{{\ModStart\Core\Util\SerializeUtil::jsonEncode($k)}}" :label="{{\ModStart\Core\Util\SerializeUtil::jsonEncode($k)}}" :value="{{\ModStart\Core\Util\SerializeUtil::jsonEncode($k)}}"></el-option>
                                    @endforeach
                                </el-select>
                            @elseif($f['type']=='link')
                                <div class="tw-flex">
                                    <div class="tw-flex-grow">
                                        <el-input v-model="value[vIndex]['{{$f['name']}}']"
                                                  placeholder="{{empty($f['placeholder'])?'':$f['placeholder']}}"
                                                  size="mini"></el-input>
                                    </div>
                                    <div>
                                        <el-button size="mini" @click="doSelectLink(vIndex,'{{$f['name']}}')">选择</el-button>
                                    </div>
                                </div>
                            @elseif($f['type']=='color')
                                <el-color-picker v-model="value[vIndex]['{{$f['name']}}']"></el-color-picker>
                            @endif
                        </td>
                    @endforeach
                    @if(!empty($itemActions))
                        <td>
                            @if(in_array('delete',$itemActions))
                                <a class="ub-lister-action ub-text-tertiary" href="javascript:;" data-tip-popover="{{L('Delete')}}" @click="value.splice(vIndex,1)">
                                    <i class="iconfont icon-trash"></i>
                                </a>
                            @endif
                            @if(in_array('copy',$itemActions))
                                <a class="ub-lister-action ub-text-tertiary" href="javascript:;" data-tip-popover="{{L('Copy')}}" @click="doValueCopy(v)">
                                    <i class="iconfont icon-copy"></i>
                                </a>
                            @endif
                            @if(in_array('sort',$itemActions))
                                <a class="ub-lister-action ub-text-tertiary" href="javascript:;" data-tip-popover="{{L('Move Up')}}" @click="doUp(value,vIndex)">
                                    <i class="iconfont icon-direction-up"></i>
                                </a>
                                <a class="ub-lister-action ub-text-tertiary" href="javascript:;" data-tip-popover="{{L('Move Down')}}" @click="doDown(value,vIndex)">
                                    <i class="iconfont icon-direction-down"></i>
                                </a>
                            @endif
                        </td>
                    @endif
                </tr>
                </tbody>
                @if($itemCanAdd)
                    <tbody>
                    <tr>
                        <td colspan="{{count($fields)+((empty($itemActions)?0:1))}}">
                            <a href="javascript:;" class="ub-text-tertiary" @click="doValueAdd">
                                <i class="iconfont icon-plus"></i>
                                {{L('Add')}}
                            </a>
                        </td>
                    </tr>
                    </tbody>
                @endif
            </table>
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
                value: {!! $value?\ModStart\Core\Util\SerializeUtil::jsonEncode($value):($defaultValue?\ModStart\Core\Util\SerializeUtil::jsonEncode($defaultValue):'[]') !!},
                icons: []
            },
            mounted() {
                @if($_hasIcon)
                    this.$api.post('{{$iconServer}}', {}, res => {
                        this.icons = res.data
                    });
                @endif
            },
            computed: {
                jsonValue: function () {
                    return JSON.stringify(this.value);
                },
                iconsFilter: function () {
                    return this.icons.filter((v) => {
                        return {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($iconGroups) !!}.includes(v.name);
                    });
                }
            },
            methods: {
                doUp: MS.collection.sort.up,
                doDown: MS.collection.sort.down,
                doValueAdd() {
                    this.value.push({!! \ModStart\Core\Util\SerializeUtil::jsonEncode($valueItem) !!});
                },
                doValueCopy(v){
                    this.value.push(JSON.parse(JSON.stringify(v)));
                },
                doSelectLink(index,name,param){
                    window.__selectorDialog = new window.api.selectorDialog({
                        server: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($linkServer) !!},
                        callback: (items) => {
                            if (items.length > 0) {
                                this.value[index][name] = items[0].link;
                            }
                        }
                    }).show();
                }
            }
        });
    });
</script>
