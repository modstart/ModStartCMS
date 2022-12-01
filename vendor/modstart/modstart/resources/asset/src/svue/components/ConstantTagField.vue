<template>
    <div>
        <span class="ub-tag" :class="colorCls" v-if="`v${data}` in values">{{values['v'+data].name}}</span>
        <span v-if="!data">-</span>
    </div>
</template>

<script>
    import {ConstantColorGuessMap} from "./ConstantScript";

    const Constants = require('./../../main/constant')
    export default {
        name: "ConstantTagField",
        props: {
            data: null,
            name: {
                type: String,
                default: '',
            },
            colors: {
                type: Object,
                default: () => {
                }
            },
            mode: {
                type: String,
                default: 'guess'
            },
            type: {
                type: Object,
                default: null
            }
        },
        computed: {
            values() {
                let vs = {}
                if (this.type) {
                    Object.keys(this.type).forEach(k => {
                        vs['v' + this.type[k].value] = {
                            name: this.type[k].name,
                            value: this.type[k].value,
                            key: k
                        }
                    })
                } else if (this.name in Constants) {
                    const v = Constants[this.name]
                    Object.keys(v).forEach(k => {
                        vs['v' + v[k].value] = {
                            name: v[k].name,
                            value: v[k].value,
                            key: k
                        }
                    })
                }
                return vs
            },
            colorCls() {
                switch (this.mode) {
                    default:
                        const k = `v${this.data}`
                        let cls = 'default'
                        if (k in this.values) {
                            const valueKey = this.values[k].key.toLowerCase()
                            ConstantColorGuessMap.forEach(o => {
                                if (o[0].test(valueKey)) {
                                    cls = o[1]
                                }
                            })
                        }
                        let ret = {}
                        ret[cls] = true
                        return ret
                }
            }
        }
    }
</script>

<style scoped>

</style>
