<style lang="less" scoped>

    .page-iframe {
        width: 100%;
    }
    .pb-html{
        color:#111;
        font-size:var(--font-size,0.65rem);
        padding:20px;
        p{
            line-height:1.5em;
        }
    }
</style>
<template>
    <el-dialog :title="data.title" :visible.sync="visible" :width="width" :height="height" top="10px" append-to-body>
        <div v-if="!!data.url">
            <iframe class="page-iframe" :src="data.url" frameborder="0" :height="iframeHeight"></iframe>
        </div>
        <div v-else>
            <div class="pb-html" v-html="data.content"></div>
        </div>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="close">关闭</el-button>
        </div>
    </el-dialog>
</template>

<script>
    import $ from 'jquery'

    export default {
        name: "ConfigArticleDialog",
        props: {
            configKey: {
                type: String,
                default: '',
            }
        },
        data() {
            return {
                visible: false,
                data: {
                    key: '',
                    title: '',
                    url: '',
                    content: '',
                }
            }
        },
        computed: {
            width() {
                return Math.min(800, $(window).width()) + 'px'
            },
            height() {
                return Math.max(400, $(window).height()) + 'px'
            },
            iframeHeight() {
                return parseInt(this.height) - 200
            }
        },
        methods: {
            show() {
                this.$dialog.loadingOn()
                this.$api.post('config_article', {key: this.configKey}, res => {
                    this.$dialog.loadingOff()
                    this.data = res.data
                    this.visible = true
                }, res => {
                    this.$dialog.loadingOff()
                })
            },
            close() {
                this.visible = false
            }
        }
    }
</script>

<style scoped>

</style>
