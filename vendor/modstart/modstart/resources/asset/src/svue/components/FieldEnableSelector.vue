<template>
    <div class="pb-field-enable-selector">
        <el-dialog :visible.sync="visible" append-to-body>
            <div slot="title">
                选择显示列
            </div>
            <div slot="footer">
                <el-button type="primary" @click="doSubmit">确认</el-button>
            </div>
            <div>
                <el-transfer v-model="datav"
                             :data="list"
                             :titles="['隐藏列','显示列']"
                             target-order="push"></el-transfer>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {Storage} from "../lib/storage";

    export default {
        name: "FieldEnableSelector",
        model: {
            prop: 'data',
            event: 'update'
        },
        props: {
            data: {
                type: Array,
                default: () => {
                    return []
                }
            },
            fields: {
                type: Array,
                default: () => []
            },
            name: {
                type: String,
                default: ''
            },
        },
        data() {
            return {
                visible: false,
                init: false,
                list: [],
                datav: [],
            }
        },
        mounted() {
            this.initData(this.fields)
        },
        watch: {
            fields: {
                handler(newValue, oldValue) {
                    this.initData(newValue)
                },
                immediate: true,
                deep: true,
            },
        },
        methods: {
            initData(fields) {
                let list = []
                fields.filter(o => o.isListable).forEach(o => {
                    list.push({
                        key: o.fname,
                        label: o.title,
                    })
                })
                this.list = list
                if (!this.init) {
                    this.init = true
                    let defaultValue = fields.filter(o => o.isListable).map(o => o.fname)
                    let datav = Storage.getArray(`FieldEnable_${this.name}`, defaultValue)
                    this.datav = datav
                    this.$emit('update', datav)
                }
            },
            edit(enableFields) {
                enableFields = enableFields || []
                let fieldNames = this.fields.filter(o => o.isListable).map(o => o.fname)
                let datav = []
                enableFields.filter(o => fieldNames.includes(o)).forEach(o => datav.push(o))
                this.datav = datav
                this.visible = true
                // console.log('edit.fields', JSON.stringify(this.fields))
                // console.log('edit.list', JSON.stringify(this.list))
                // console.log('edit', JSON.stringify(this.datav))
            },
            hide() {
                this.visible = false
            },
            doSubmit() {
                let datav = this.datav
                this.$emit('update', datav)
                Storage.set(`FieldEnable_${this.name}`, datav)
                this.visible = false
            }
        }
    }
</script>

<style scoped>

</style>
