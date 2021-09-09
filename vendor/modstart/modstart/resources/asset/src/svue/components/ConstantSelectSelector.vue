<template>
    <el-select v-model="datav" placeholder="请选择">
        <el-option v-if="hasEmpty" :key="'x0'" label="-" :value="0"></el-option>
        <el-option
                v-for="(item,itemIndex) in values"
                :key="itemIndex"
                :label="item.name"
                :value="item.value">
        </el-option>
    </el-select>
</template>

<script>
    import {SelectorIdMixin} from "./Mixins/selector";

    const Constants = require('./../../main/constant')
    export default {
        name: "ConstantSelectSelector",
        mixins: [SelectorIdMixin],
        props: {
            hasEmpty: {
                type: Boolean,
                default: true,
            },
            name: {
                type: String,
                default: '',
            },
            type: {
                type: Object,
                default: null
            }
        },
        computed: {
            values() {
                let vs = []
                if (null !== this.type) {
                    Object.keys(this.type).forEach(k => {
                        vs.push({
                            name: this.type[k].name,
                            value: this.type[k].value,
                            key: k
                        })
                    })
                } else if (this.name in Constants) {
                    const v = Constants[this.name]
                    Object.keys(v).forEach(k => {
                        vs.push({
                            name: v[k].name,
                            value: v[k].value,
                            key: k
                        })
                    })
                }
                return vs
            }
        }
    }
</script>
