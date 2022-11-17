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
                    </div>
                    <div class="field">
                        <el-switch v-model="data.isRequired"></el-switch>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        后台搜索
                    </div>
                    <div class="field">
                        <el-switch v-model="data.isSearch"></el-switch>
                    </div>
                </div>
                <div class="line">
                    <div class="label">
                        列表显示
                    </div>
                    <div class="field">
                        <el-switch v-model="data.isList"></el-switch>
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
                        ModelFieldType: {!! json_encode(\Module\Cms\Field\CmsField::allMap()) !!},
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
                            placeholder: ""
                        }, {!! json_encode($record) !!}),
                        nameReadOnly: false
                    }
                },
                watch: {
                    data: {
                        handler(n, o) {
                            if(!FieldCustomScript[this.data.fieldType]){
                                return;
                            }
                            if(!FieldCustomScript[this.data.fieldType]['onDataChange']){
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
                            MS.api.defaultCallback(res,{
                                success:res=>{
                                    MS.dialog.loadingOff()
                                    parent.__grids.get(0).lister.refresh()
                                    parent.layer.closeAll()
                                },
                                error:res=>{
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
