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
        }
    },
    data() {
        return {
            id: 'UploadButtonUploader_' + StrUtil.randomString(10),
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
            if (!!window.__dataConfig) {
                return window.__dataConfig
            }
            return {
                chunkSize: 0,
                category: {
                    [this.category]: {
                        extensions: [],
                        maxSize: 0
                    }
                }
            }
        }
    },
    mounted() {
        var $this = this
        const init = () => {
            const url = `${this.url}/${this.category}`
            let text = '<span class="tw-px-4 tw-rounded tw-border tw-border-solid tw-border-gray-200" style="display:block;line-height:28px;"><i class="iconfont icon-upload"></i> ' + this.L('Select Local File') + '</span>'
            if (this.size === 'lg') {
                text = '<span class="tw-px-4 tw-rounded tw-border tw-border-solid tw-border-gray-200 tw-rounded-lg tw-py-10" style="display:block;"><i class="iconfont icon-upload" style="font-size:2rem;"></i><br /> ' + this.L('Select Local File') + '</span>'
            }
            UploadButtonUploader('#' + this.id, {
                text,
                // swf: '/static/webuploader/Uploader.swf',
                server: this.$api.url ? this.$api.url(url) : url,
                extensions: this.dataUploadConfig.category[this.category].extensions.join(','),
                sizeLimit: this.dataUploadConfig.category[this.category].maxSize,
                chunkSize: this.dataUploadConfig.chunkSize,
                callback: function (file, me) {
                    $this.$emit('success', file)
                },
                finish: function () {
                    $this.$emit('finish')
                }
            })
        }
        const check = () => {
            setTimeout(() => {
                init()
            }, 1000)
        }
        check()
    }
}

</script>

<style lang="less">
@import "webuploader/css.less";
</style>
