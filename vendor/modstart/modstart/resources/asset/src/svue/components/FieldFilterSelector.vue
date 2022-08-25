<template>
    <div class="pb-field-filter-selector">
        <div v-if="dataView.length>0">
            <template v-for="(item,itemIndex) in dataView">
                <el-tag :key="itemIndex" closable type="info" @close="doDeleteView(itemIndex)">
                    {{viewItem(item)}}
                </el-tag>
            </template>
        </div>
        <el-dialog :visible.sync="visible" append-to-body width="80%">
            <div slot="title">
                高级筛选
            </div>
            <div slot="footer">
                <el-button type="primary" @click="doSubmit">确认</el-button>
            </div>
<!--            <div>-->
<!--                <pre>{{ JSON.stringify(datav,null,2) }}</pre>-->
<!--            </div>-->
            <div>
                <template v-for="(item,itemIndex) in datav">
                    <el-row>
                        <el-col :span="8">
                            <el-select v-model="item.field" @change="onFieldChange(itemIndex)" placeholder="请选择要筛选的字段名">
                                <el-option v-for="fieldItem in fields"
                                           :key="fieldItem.fname"
                                           :label="fieldItem.title"
                                           :value="fieldItem.fname">
                                </el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="4" v-if="conditionVisible(item.type)">
                            <el-select v-model="item.condition" placeholder="请选择条件"
                                       @change="onConditionChange(itemIndex)">
                                <el-option v-for="conditionItem in condition(item.type)"
                                           :key="conditionItem.value"
                                           :label="conditionItem.label"
                                           :value="conditionItem.value">
                                </el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="10" v-if="valueVisible(item.type,item.condition)">
                            <component v-bind:is="valueComponent(item.type,item.condition)"
                                       :field="field(item.field)"
                                       v-model="item.value"></component>
                        </el-col>
                        <el-col :span="1" class="delete">
                            <i class="el-icon-error delete-btn" @click="datav.splice(itemIndex,1)"></i>
                        </el-col>
                    </el-row>
                </template>
                <el-button @click="doAdd">+ 添加筛选条件</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {FieldFilterManager} from "../lib/fields-config";
    import {FieldFilters} from "../lib/fields";

    export default {
        name: "FieldFilterSelector",
        components: {...FieldFilters},
        props: {
            fields: {
                type: Array,
                default: () => []
            },
        },
        data() {
            return {
                visible: false,
                datav: [],
                dataView: [],
            }
        },
        methods: {
            // 自定义修改 开始
            onFieldChange(itemIndex) {
                switch (this.datav[itemIndex].type) {
                    default:
                        let exists = this.fields.filter(o => o.fname === this.datav[itemIndex].field)
                        this.datav[itemIndex].type = exists[0].type
                        this.datav[itemIndex].condition = FieldFilterManager.conditionDefault(this.datav[itemIndex].type)
                        this.onConditionChange(itemIndex)
                        break
                }
            },
            onConditionChange(itemIndex) {
                switch (this.datav[itemIndex].type) {
                    default:
                        this.datav[itemIndex].value = FieldFilterManager.value(
                            this.datav[itemIndex].type,
                            this.datav[itemIndex].condition
                        )
                        break
                }
            },
            conditionVisible(type) {
                switch (type) {
                    default:
                        return FieldFilterManager.conditionVisible(type)
                }
            },
            condition(type) {
                switch (type) {
                    default:
                        return FieldFilterManager.condition(type)
                }
            },
            valueVisible(type, condition) {
                switch (type) {
                    default:
                        return FieldFilterManager.valueVisible(type, condition)
                }
            },
            valueComponent(type, condition) {
                switch (type) {
                    default:
                        return FieldFilterManager.valueComponent(type, condition)
                }
            },
            viewItem(item) {
                switch (item.type) {
                    default:
                        let exists = this.fields.filter(o => o.fname === item.field)
                        return exists[0].title + ' ' + FieldFilterManager.view(item.type, item.condition, item.value)
                }
            },
            // 自定义修改 结束

            doAdd() {
                this.datav.push({
                    type: '',
                    field: '',
                    condition: '',
                    value: '',
                })
            },
            field(field) {
                let exists = this.fields.filter(o => o.fname === field)
                return exists[0]
            },
            edit(datav) {
                this.datav = JSON.parse(JSON.stringify(datav))
                this.visible = true
            },
            view(datav) {
                this.dataView = JSON.parse(JSON.stringify(datav))
            },
            hide() {
                this.visible = false
            },
            doDeleteView(itemIndex) {
                this.dataView.splice(itemIndex, 1)
                this.$emit('submit', this.dataView)
            },
            doSubmit() {
                this.$emit('submit', this.datav)
                this.visible = false
            }
        }
    }
</script>

<style scoped lang="less">
    .el-row {
        margin-bottom: 20px;
        .delete-btn {
            margin-left: 15px;
            margin-top: 10px;
            color: #bbb;
            cursor: pointer;
        }
    }
</style>
