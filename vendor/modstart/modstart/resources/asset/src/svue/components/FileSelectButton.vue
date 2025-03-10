<script>
export default {
    name: "FileSelectButton",
    props: {
        extensions: {
            type: Array,
            default: () => []
        },
    },
    computed:{
        accept() {
            if (this.extensions.length) {
                return this.extensions.map(e => '.' + e).join(',')
            }
            return ''
        }
    },
    methods: {
        onFileChange(file) {
            this.$emit('change', file.raw)
        }
    }
}
</script>

<template>
    <div class="pb-file-select-button">
        <el-upload :on-change="onFileChange"
                   :auto-upload="false"
                   :accept="accept"
                   :show-file-list="false">
            <slot></slot>
        </el-upload>
    </div>
</template>

<style lang="less">
.pb-file-select-button{
    .el-upload{
        display:block;
    }
}
</style>
