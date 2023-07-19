<template>
    <div class="pb-values-editor">
        <table class="ub-table mini tw-bg-white border">
            <tbody>
            <tr v-for="(c,cIndex) in currentData">
                <td>
                    <el-input size="mini" v-model="currentData[cIndex]"/>
                </td>
                <td width="60">
                    <a href="javascript:;" class="ub-text-muted" v-if="cIndex>0" @click="doUp(currentData,cIndex)">
                        <i class="iconfont icon-direction-up"></i>
                    </a>
                    <a href="javascript:;" class="ub-text-muted" v-if="cIndex<currentData.length-1"
                       @click="doDown(currentData,cIndex)">
                        <i class="iconfont icon-direction-down"></i>
                    </a>
                    <a href="javascript:;" class="ub-text-muted" @click="currentData.splice(cIndex,1)">
                        <i class="iconfont icon-trash"></i>
                    </a>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <a href="javascript:;" class="ub-text-muted" @click="currentData.push('')">
                        <i class="iconfont icon-plus"></i>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import {FieldVModel} from "../lib/fields-config";
import {Collection} from './../lib/collection'

export default {
    name: "ValuesEditor",
    mixins: [FieldVModel],
    props: {
        data: {
            type: Array,
            default: []
        },
    },
    methods: {
        doUp: Collection.sort.up,
        doDown: Collection.sort.down,
    }
}
</script>

<style lang="less" scoped>
.pb-values-editor {
    min-width: 5rem;
}
</style>
