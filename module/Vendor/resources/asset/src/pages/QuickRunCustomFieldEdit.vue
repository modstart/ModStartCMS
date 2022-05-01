<template>
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
                    <el-input v-model="data.name" @focus="doGenerateName">
                        <template slot="prepend" v-if="!!fieldNamePrefix">{{fieldNamePrefix}}</template>
                    </el-input>
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
                        <el-option
                            v-for="k in Object.keys(CustomFieldType)"
                            :key="k"
                            :label="CustomFieldType[k].name"
                            :value="CustomFieldType[k].value">
                        </el-option>
                    </el-select>
                </div>
            </div>
            <div class="line" v-if="['text','textarea','radio','select','checkbox'].includes(data.fieldType)">
                <div class="label">
                    <span>*</span>
                    字段长度
                </div>
                <div class="field">
                    <el-input-number v-model="data.maxLength"></el-input-number>
                </div>
            </div>
            <div class="line"
                 v-if="[CustomFieldType.RADIO.value,CustomFieldType.SELECT.value,CustomFieldType.CHECKBOX.value].includes(data.fieldType)">
                <div class="label">
                    <span>*</span>
                    选项
                </div>
                <div class="field">
                    <table class="ub-table mini tw-bg-white">
                        <thead>
                        <tr>
                            <th>值</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(option,optionIndex) in data.fieldData.options">
                            <td>
                                <el-input v-model="data.fieldData.options[optionIndex]" placeholder="输入值"></el-input>
                            </td>
                            <td>
                                <a href="javascript:;" class="ub-text-danger"
                                   @click="data.fieldData.options.splice(optionIndex,1)">
                                    <i class="iconfont icon-trash"></i>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                        <tbody>
                        <tr>
                            <td colspan="2">
                                <a href="javascript:;" class="ub-text-muted" @click="data.fieldData.options.push('')">
                                    <i class="iconfont icon-plus"></i>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
</template>

<script>
const pinyin = require('pinyin')
const CustomFieldType = window.__data.CustomFieldType
export default {
    name: "QuickRunCustomFieldEdit",
    data() {
        return {
            CustomFieldType,
            fieldNamePrefix: window.__data.fieldNamePrefix,
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
            }, window.__data.record),
            nameReadOnly: false
        }
    },
    watch: {
        data: {
            handler(n, o) {
                switch (this.data.fieldType) {
                    case CustomFieldType.RADIO.value:
                    case CustomFieldType.SELECT.value:
                    case CustomFieldType.CHECKBOX.value:
                        if (!('options' in this.data.fieldData)) {
                            this.$set(this.data, 'fieldData', {options: []})
                        }
                        break
                }
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
            value = pinyin(this.data.title, {
                style: pinyin.STYLE_NORMAL,
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
                value = pinyin(this.data.title, {
                    style: pinyin.STYLE_INITIALS,
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
            this.$dialog.loadingOn()
            this.$api.post(window.location.href, {data: JSON.stringify(this.data)}, res => {
                this.$dialog.loadingOff()
                parent.__grids.get(0).lister.refresh()
                parent.layer.closeAll()
            }, res => {
                this.$dialog.loadingOff()
            })
        }
    }
}
</script>


