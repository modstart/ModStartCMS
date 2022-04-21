<template>
    <div>
        <div style="margin:0 auto;" class="pb-upload-button"
             :style="{maxWidth:width}"
             :id="id"
        ></div>
    </div>
</template>

<script>
import {UploadButtonUploader} from "./UploadButton/uploader";
import {StrUtil} from "../lib/util";

export default {
    name: "UploadButton",
    props: {
        url: {
            type: String,
            default: '/path_to_url',
        },
        category: {
            type: String,
            default: 'file',
        },
        uploadConfig: {
            type: Object,
            default: null
        },
        width: {
            type: String,
            default: '200px'
        },
        size: {
            type: String,
            default: null
        },
        uploadBeforeCheck: {
            type: Function,
            default: null
        }
    },
    data() {
        return {
            id: 'UploadButtonUploader_' + StrUtil.randomString(10),
            dataConfigFromServer: null,
        }
    },
    computed: {
        dataUploadConfig() {
            if (!!this.uploadConfig) {
                return this.uploadConfig
            }
            if (!!this.$store) {
                return this.$store.state.base.config.dataUpload
            }
            if (!window.__dataConfigLoading) {
                window.__dataConfigLoading = true
                this.initConfig()
            }
            return this.dataConfigFromServer
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        initConfig() {
            this.$api.post(`${this.url}/${this.category}`, {action: 'config'}, res => {
                this.dataConfigFromServer = res.data
            })
        },
        init() {
            if (!this.dataUploadConfig) {
                setTimeout(() => {
                    this.init()
                }, 100)
                return
            }
            const url = `${this.url}/${this.category}`
            let text = '<span class="tw-px-4 tw-rounded tw-border tw-border-solid tw-border-gray-200" style="display:block;line-height:28px;"><i class="iconfont icon-upload"></i> ' + this.L('Select Local File') + '</span>'
            if (this.size === 'lg') {
                text = '<span class="tw-px-4 tw-rounded tw-border tw-border-solid tw-border-gray-200 tw-rounded-lg tw-py-10" style="display:block;"><i class="iconfont icon-upload" style="font-size:2rem;"></i><br /> ' + this.L('Select Local File') + '</span>'
            }
            const $this = this
            UploadButtonUploader('#' + this.id, {
                text,
                server: this.$api.url ? this.$api.url(url) : url,
                extensions: this.dataUploadConfig.category[this.category].extensions.join(','),
                sizeLimit: this.dataUploadConfig.category[this.category].maxSize,
                chunkSize: this.dataUploadConfig.chunkSize,
                uploadBeforeCheck: this.uploadBeforeCheck,
                callback: function (file, me) {
                    $this.$emit('success', file)
                },
                finish: function () {
                    $this.$emit('finish')
                }
            })
        }
    }
}

</script>

<style lang="less">
@import "webuploader/css.less";
</style>
