<template>
    <el-radio-group v-model="datav">
        <el-radio v-for="(item,itemIndex) in values" :label="item.value" :key="itemIndex">{{item.name}}</el-radio>
    </el-radio-group>
</template>

<script>
    import {SelectorIdMixin} from "./Mixins/selector";

    const Constants = require('./../../main/constant')
    export default {
        name: "ConstantRadioSelector",
        mixins: [SelectorIdMixin],
        props: {
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