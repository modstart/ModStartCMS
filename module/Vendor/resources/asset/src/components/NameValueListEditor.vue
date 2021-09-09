<template>
    <div style="display:inline-block;">
        <div class="tw-py-1" v-for="(link,linkIndex) in currentData">
            <el-input style="width:5rem;" v-model="currentData[linkIndex].name" placeholder="名称"></el-input>
            <el-input style="width:10rem;" v-model="currentData[linkIndex].value" placeholder="内容"></el-input>
            <a href="javascript:;" class="ub-text-muted" @click="currentData.splice(linkIndex,1)">
                <i class="iconfont icon-trash"></i>
            </a>
        </div>
        <div class="tw-py-1">
            <a href="javascript:;" class="ub-text-muted" @click="currentData.push({name: '', value: ''})">
                <i class="iconfont icon-plus"></i> 增加
            </a>
        </div>
    </div>
</template>

<script>
    import {FieldInputMixin, FieldVModel} from "@ModStartAsset/svue/lib/fields-config";
    export default {
        name: "NameValueListEditor",
        mixins: [FieldInputMixin, FieldVModel],
    }
</script>
