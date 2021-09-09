<template>
    <div>
        <el-cascader :options="options" v-model="datav"
                     :props="{value:'title',label:'title',children:'_child'}"
                     style="width:100%;">
        </el-cascader>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";
    import {Tree} from "../../lib/tree";

    export default {
        name: "AreaObjectInput",
        mixins: [FieldInputMixin],
        props: {
            provinceKey: {
                type: String,
                default: 'province',
            },
            cityKey: {
                type: String,
                default: 'city',
            },
            districtKey: {
                type: String,
                default: 'district',
            },
        },
        data() {
            return {
                options: [],
                datav: ['', '', ''],
            }
        },
        watch: {
            datav(newValue, oldValue) {
                if (null === this.datav) {
                    this.datav = ['', '', '']
                    return
                }
                const v = [
                    this.data[this.provinceKey] || '',
                    this.data[this.cityKey] || '',
                    this.data[this.districtKey] || '',
                ]
                if (JSON.stringify(newValue) !== JSON.stringify(v)) {
                    this.data[this.provinceKey] = newValue[0]
                    this.data[this.cityKey] = newValue[1]
                    this.data[this.districtKey] = newValue[2]
                    this.$emit('update', this.data)
                }
            },
            data: {
                handler(newValue, oldValue) {
                    const v = [
                        this.data[this.provinceKey] || '',
                        this.data[this.cityKey] || '',
                        this.data[this.districtKey] || '',
                    ]
                    if (JSON.stringify(v) !== JSON.stringify(this.datav)) {
                        this.datav = v
                    }
                },
                deep: true,
                immediate: true
            },
        },
        mounted() {
            this.$api.post('area/china', {}, res => {
                this.options = Tree.tree(res.data, 0, 'id', 'pid', 'sort')
            })
        }
    }
</script>
