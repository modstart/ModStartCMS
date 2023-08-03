<template>
    <div>
        <div class="ub-upload-button"
             :class="{queue:styles==='queue'}"
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
        },
        autoSave: {
            type: Boolean,
            default: false
        },
        styles: {
            type: String,
            default: 'simple',
        },
        uploadText: {
            type: String,
            default: null
        },
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
            if (!window.__dataConfigLoading || !window.__dataConfigLoading[this.category]) {
                window.__dataConfigLoadingId = Object.assign({
                    [this.category]: this.id
                }, window.__dataConfigLoadingId)
                window.__dataConfigLoading = Object.assign({
                    [this.category]: true
                }, window.__dataConfigLoading)
                this.initConfig()
            } else {
                // 有可能是其他组件在加载
                if (window.__dataConfigLoadingId && window.__dataConfigLoadingId[this.category] != this.id) {
                    const timer = setInterval(() => {
                        if (window.__dataConfigFromServer && window.__dataConfigFromServer[this.category]) {
                            this.dataConfigFromServer = window.__dataConfigFromServer[this.category]
                            clearInterval(timer)
                        }
                    }, 100)
                }
            }
            return this.dataConfigFromServer
        },
        apiUrl() {
            const url = `${this.url}/${this.category}`
            return this.$api.url ? this.$api.url(url) : url
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        initConfig() {
            this.$api.post(`${this.url}/${this.category}`, {action: 'config'}, res => {
                this.dataConfigFromServer = res.data
                window.__dataConfigFromServer = Object.assign({
                        [this.category]: res.data
                    },
                    window.__dataConfigFromServer
                )
            })
        },
        init() {
            if (!this.dataUploadConfig) {
                setTimeout(() => {
                    this.init()
                }, 100)
                // console.log('Wait dataUploadConfig')
                return
            }
            let uploadText = this.uploadText || this.L('Select Local File')
            let text = '<div class="btn btn-block"><i class="iconfont icon-upload"></i> ' + uploadText + '</div>'
            if (this.size === 'lg') {
                text = '<span class="btn btn-block btn-lg"><i class="iconfont icon-upload"></i> ' + uploadText + '</span>'
            }
            const $this = this
            UploadButtonUploader('#' + this.id, {
                text,
                server: this.apiUrl,
                extensions: this.dataUploadConfig.category[this.category].extensions.join(','),
                sizeLimit: this.dataUploadConfig.category[this.category].maxSize,
                chunkSize: this.dataUploadConfig.chunkSize,
                uploadBeforeCheck: this.uploadBeforeCheck,
                callback: (file, me) => {
                    $this.$emit('success', file)
                    if (this.autoSave) {
                        this.$api.post(this.apiUrl, {
                            action: 'save',
                            path: file.path,
                            name: file.name,
                            size: file.size,
                            categoryId: 0
                        }, res => {
                            $this.$emit('save', res.data)
                        })
                    }
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
@import "webuploader/webuploader.css";

.ub-upload-button {
    // Wenku 上传时高度不显示进度条，先注释掉
    // height: 1.6rem;
    overflow: hidden;
}
</style>
