<template>
    <div>
        <div style="max-width:200px;margin:0 auto;" class="pb-upload-button"
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
                UploadButtonUploader('#' + this.id, {
                    text: '<span class="tw-px-4 tw-rounded tw-border tw-border-solid tw-border-gray-200" style="display:block;line-height:28px;"><i class="iconfont icon-upload"></i> ' + this.L('Select Local File') + '</span>',
                    // swf: '/static/webuploader/Uploader.swf',
                    server: this.$api.url ? this.$api.url(url) : url,
                    extensions: this.dataUploadConfig.category[this.category].extensions.join(','),
                    sizeLimit: this.dataUploadConfig.category[this.category].maxSize,
                    chunkSize: this.dataUploadConfig.chunkSize,
                    callback: function (file, me) {
                        $this.$emit('success', file)
                    },
                    finish: function () {
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
