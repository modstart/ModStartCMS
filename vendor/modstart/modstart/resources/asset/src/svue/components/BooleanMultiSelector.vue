<template>
    <div class="pb-boolean-multi-selector">
        <el-checkbox v-if="hasCheckAll" class="checkbox-all" v-model="checkAll">全部</el-checkbox>
        <el-checkbox-group v-model="datav" placeholder="请选择"
                           :class="{border:hasBorder}">
            <span v-if="hasCheckAll" style="width: 60px;height:20px;display:inline-block;"></span>
            <el-checkbox v-for="item in values" :key="item.key" :label="item.value">{{item.name}}</el-checkbox>
        </el-checkbox-group>
    </div>
</template>

<script>
    import {SelectorIdsMixin} from "./Mixins/selector";

    const Constants = require('./../../main/constant')
    export default {
        name: "BooleanMultiSelector",
        mixins: [SelectorIdsMixin],
        props: {
            hasBorder: {
                type: Boolean,
                default: true,
            },
            hasCheckAll: {
                type: Boolean,
                default: false,
            },
            trueLabel: {
                type: String,
                default: '启用'
            },
            falseLabel: {
                type: String,
                default: '禁用'
            },
        },
        data() {
            return {
                checkAll: false,
            }
        },
        watch: {
            checkAll: {
                handler(n, o) {
                    if (this.checkAll) {
                        if (this.datav.length !== this.values.length) {
                            this.datav = this.values.map(o => o.value)
                        }
                    } else {
                        if (this.datav.length === this.values.length) {
                            this.datav = []
                        }
                    }
                }
            },
            datav: {
                handler(n, o) {
                    if (this.values.length === this.datav.length) {
                        this.checkAll = true
                    } else {
                        this.checkAll = false
                    }
                },
                deep: true,
            }
        },
        computed: {
            values() {
                return [
                    {name: this.trueLabel, value: true, key: true},
                    {name: this.falseLabel, value: false, key: false},
                ]
            }
        }
    }
</script>

<style lang="less" scoped>
    .pb-boolean-multi-selector {
        position: relative;

        .checkbox-all {
            position: absolute;
            left: 0;
            top: 0;
        }

        .border {
            border: 1px solid #EEE;
            border-radius: 3px;
            padding: 2px 10px;
        }
    }
</style>