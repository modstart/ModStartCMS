@extends('modstart::admin.dialogFrame')

@section('pageTitle')字段管理@endsection

@section('bodyContent')
    <div id="app" v-cloak>
        <div>
            <div class="ub-form">
                <div class="line">
                    <div class="label">
                        <span>*</span>
                        名称
                    </div>
                    <div class="field">
                        <el-input v-model="data.title"></el-input>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        <span>*</span>
                        标识
                    </div>
                    <div class="field">
                        <el-input v-model="data.name" @focus="doGenerateName"></el-input>
                        <div class="help">
                            数字字母组成，推荐驼峰命名方式，首字母不能是数字
                        </div>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        <span>*</span>
                        启用
                    </div>
                    <div class="field">
                        <el-switch v-model="data.enable"></el-switch>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        <span>*</span>
                        类型
                    </div>
                    <div class="field">
                        <el-select v-model="data.fieldType">
                            <el-option v-for="(v,k) in ModelFieldType" :key="k" :label="v" :value="k"></el-option>
                        </el-select>
                    </div>
                </div>
                @foreach(\Module\Cms\Field\CmsField::all() as $f)
                    <div v-if="'{{ $f->name()  }}'===data.fieldType">
                        {!! $f->renderForFieldEdit() !!}
                    </div>
                @endforeach
                <div class="line">
                    <div class="label">
                        输入提示
                    </div>
                    <div class="field">
                        <el-input v-model="data.placeholder"></el-input>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        是否必填
                        <i class="iconfont icon-warning"
                           data-tip-popover="后台编辑时是否必填"></i>
                    </div>
                    <div class="field">
                        <el-switch v-model="data.isRequired"></el-switch>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        列表搜索
                        <i class="iconfont icon-warning"
                           data-tip-popover="开启后后台列表页面可搜索该字段，前台可通过链接参数搜索该字段（前台需设置访客列表页面可见）。"></i>
                    </div>
                    <div class="field">
                        <el-switch v-model="data.isSearch"></el-switch>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        后台列表显示
                    </div>
                    <div class="field">
                        <el-switch v-model="data.isList"></el-switch>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        访客列表页面可见
                        <i class="iconfont icon-warning"
                           data-tip-popover="关闭后，在前台列表页面不可见该字段"></i>
                    </div>
                    <div class="field">
                        <el-switch v-model="data.guestVisitVisible"></el-switch>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bodyAppend')
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script src="@asset('vendor/Cms/entry/pinyin.js')"></script>
    <script>
        Vue.use(ELEMENT, {size: 'mini', zIndex: 3000});
        $(function () {
            var FieldCustomScript = {};
            @foreach(\Module\Cms\Field\CmsField::all() as $f)
                FieldCustomScript['{{ $f->name()  }}'] = {!! $f->renderForFieldEditScript() !!};
            @endforeach
            new Vue({
                el: '#app',
                data() {
                    return {
                        ModelFieldType: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\Module\Cms\Field\CmsField::allMap()) !!},
                        data: Object.assign({
                            title: "",
                            name: "",
                            enable: true,
                            fieldType: "text",
                            fieldData: {},
                            maxLength: 100,
                            isRequired: true,
                            isSearch: true,
                            isList: true,
                            guestVisitVisible: false,
                            placeholder: ""
                        }, {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($record) !!}),
                        nameReadOnly: false
                    }
                },
                watch: {
                    data: {
                        handler(n, o) {
                            if (!FieldCustomScript[this.data.fieldType]) {
                                return;
                            }
                            if (!FieldCustomScript[this.data.fieldType]['onDataChange']) {
                                return;
                            }
                            FieldCustomScript[this.data.fieldType]['onDataChange'].call(this);
                        },
                        immediate: true,
                        deep: true
                    }
                },
                mounted() {
                    $(() => {
                        $('.ub-panel-dialog').removeClass('no-foot')
                            .find('.panel-dialog-foot').show()
                            .find('[data-submit]').show().on('click', () => this.doSubmit())
                    })
                },
                methods: {
                    capitalizeFirstLetter(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    },
                    doGenerateName() {
                        if (this.nameReadOnly) {
                            return
                        }
                        if (!this.data.title) {
                            return
                        }
                        if(this.data.name){
                            return
                        }
                        let value, s, i
                        value = MS.vendor.pinyin(this.data.title, {
                            style: MS.vendor.pinyin.STYLE_NORMAL,
                        })
                        s = []
                        for (i = 0; i < value.length; i++) {
                            if (s.length) {
                                s.push(this.capitalizeFirstLetter(value[i].join('')))
                            } else {
                                s.push(value[i].join(''))
                            }
                        }
                        s = s.join('')
                        if (s.length > 50) {
                            value = MS.vendor.pinyin(this.data.title, {
                                style: MS.vendor.pinyin.STYLE_INITIALS,
                            })
                            s = []
                            for (i = 0; i < value.length; i++) {
                                if (s.length) {
                                    s.push(this.capitalizeFirstLetter(value[i].join('')))
                                } else {
                                    s.push(value[i].join(''))
                                }
                            }
                            s = s.join('')
                        }
                        this.data.name = s
                    },
                    doSubmit() {
                        MS.dialog.loadingOn()
                        MS.api.post(window.location.href, {data: JSON.stringify(this.data)}, res => {
                            MS.api.defaultCallback(res, {
                                success: res => {
                                    MS.dialog.loadingOff()
                                    parent.__grids.get(0).lister.refresh()
                                    parent.layer.closeAll()
                                },
                                error: res => {
                                    MS.dialog.loadingOff()
                                }
                            })
                        })
                    }
                }
            });
        });
    </script>
@endsection
