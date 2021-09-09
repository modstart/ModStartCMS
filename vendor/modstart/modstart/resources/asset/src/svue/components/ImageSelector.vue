<template>
    <div>
        <div class="pb-image-selector" :style="{backgroundImage:'url('+data+')',width:width,height:height}">
            <div class="mask">
                <a href="javascript:;" v-if="!!data && previewShow"
                   @click="doPreview()"
                   :style="{lineHeight:height}"><i class="iconfont icon-zoom-in"></i></a>
                <a href="javascript:;" v-if="!!data" @click="doDelete()"
                   :style="{lineHeight:height}"><i class="iconfont icon-trash"></i></a>
                <a href="javascript:;" v-if="!uploadDirectMode && !data" @click="doSelect()"
                   :style="{lineHeight:height}"><i class="iconfont icon-plus"></i></a>
                <el-upload
                        v-if="uploadDirectMode && !data"
                        :action="directUploadUrl"
                        :style="{lineHeight:height}"
                        :data="{action:'uploadDirect'}"
                        :show-file-list="false"
                        :on-success="handleAvatarSuccess">
                    <i class="iconfont icon-plus"></i>
                </el-upload>
            </div>
            <a href="javascript:;" :class="{plus:true,'has-data':!!data}" :style="{lineHeight:height}">
                <i class="iconfont icon-plus"></i>
            </a>
        </div>
        <el-dialog class="pb-image-selector-preview" :visible.sync="previewVisible" append-to-body>
            <img width="100%" :src="data"/>
        </el-dialog>
        <DataSelector ref="imageDialog"
                      :url="imageDialogUrl"
                      category="image"
                      :child-key="childKey"
                      @on-select="doImageSelect"
        />
    </div>
</template>

<script>
    import DataSelector from "./DataSelector";

    export default {
        name: "ImageSelector",
        components: {DataSelector},
        model: {
            prop: 'data',
            event: 'ImageSelectorEvent'
        },
        props: {
            data: {
                type: String,
                default: ''
            },
            imageDialogUrl: {
                type: String,
                default: 'member_data/file_manager'
            },
            width: {
                type: String,
                default: '60px',
            },
            height: {
                type: String,
                default: '60px',
            },
            previewShow: {
                type: Boolean,
                default: true,
            },
            uploadDirectMode: {
                type: Boolean,
                default: false,
            },
            childKey: {
                type: String,
                default: '_child',
            },
            doSelectCustom: {
                type: Function,
                default: null,
            }
        },
        data() {
            return {
                datav: '',
                previewVisible: false,
            }
        },
        watch: {
            data(newValue, oldValue) {
                if (newValue !== this.datav) {
                    this.datav = newValue
                }
            }
        },
        computed: {
            directUploadUrl() {
                return this.imageDialogUrl + '/image'
            }
        },
        methods: {
            doImageSelect(files) {
                this.datav = files[0].path
                this.$emit('ImageSelectorEvent', this.datav)
            },
            doDelete() {
                this.datav = ''
                this.$emit('ImageSelectorEvent', '')
            },
            doPreview() {
                this.previewVisible = true
            },
            doSelect() {
                if (null === this.doSelectCustom) {
                    this.$refs.imageDialog.show()
                } else {
                    this.doSelectCustom(path => {
                        this.datav = path
                        this.$emit('ImageSelectorEvent', path)
                    })
                }
            },
            handleAvatarSuccess(res, file) {
                if (res.code) {
                    this.$dialog.tipError(res.msg)
                    return
                }
                this.datav = res.data.path
                this.$emit('ImageSelectorEvent', this.datav)
            },
        }
    }
</script>

<style lang="less">
    .pb-image-selector-preview {
        .el-dialog__header {
            border: none;
            height: 0;
            padding: 0;

            .el-dialog__headerbtn {
                width: 30px;
                background: rgba(0, 0, 0, 0.8);
                color: #FFF;
                font-size: 20px;
                border-radius: 50%;
                height: 30px;
                margin-right: -30px;
                margin-top: -20px;
            }
        }

        .el-dialog__body {
            padding: 0;
        }
    }

    .pb-image-selector {
        overflow: hidden;
        background-color: #fff;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        border: 1px solid #c0ccda;
        border-radius: 6px;
        box-sizing: border-box;
        display: inline-block;

        &:hover {
            .mask {
                display: block;
            }
        }

        .plus {
            line-height: 60px;
            text-align: center;
            color: #999;
            display: block;

            &.has-data {
                display: none;
            }
        }

        .mask {
            background: rgba(0, 0, 0, 0.5);
            color: #FFF;
            text-align: center;
            display: none;

            a {
                color: #CCC;
                display: inline-block;
                line-height: 60px;
                padding: 0 5px;

                &:hover {
                    color: #FFF;
                }
            }
        }
    }
</style>
