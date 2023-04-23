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
        <div id="{{$id}}Input" class="tw-bg-white tw-rounded tw-p-2" v-cloak>
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            @if(\ModStart\Field\ManyRelation::MODE_GROUP_TAGS==$mode)
                <table class="ub-table">
                    <tr v-for="(gt,gtIndex) in groupTags" :key="gtIndex">
                        <td style="width:6em;vertical-align:top;">{!! '{'.'{ gt.'.$groupTagsTitleKey.' }'.'}' !!}</td>
                        <td>
                            <el-checkbox v-for="(gtItem,gtIndex) in gt.{{ $groupTagsChildKey }}"
                                         :key="gtIndex"
                                         :value="value.includes(gtItem.id)"
                                         @change="checked=>onChange(checked,gtItem.id)"
                            >
                                @{{ gtItem.title }}
                            </el-checkbox>
                        </td>
                    </tr>
                </table>
            @endif
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
                groupTags: {!! json_encode($groupTags) !!},
                value: {!! json_encode(null===$value?$defaultValue:$value) !!}
            },
            computed: {
                jsonValue: function () {
                    return JSON.stringify(this.value);
                }
            },
            methods: {
                onChange(checked, id) {
                    if (checked) {
                        if (!this.value.includes(id)) {
                            this.value.push(id)
                        }
                    } else {
                        if (this.value.includes(id)) {
                            this.value.splice(this.value.indexOf(id), 1)
                        }
                    }
                }
            }
        });
    });
</script>
