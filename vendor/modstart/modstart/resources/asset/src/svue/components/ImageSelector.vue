<template>
    <div>
        <div class="tw-mb-1" v-if="currentData">
            <div class="ub-image-selector has-value">
                <div class="tools">
                    <a href="javascript:;" class="action close" @click="doDelete()"><i class="iconfont icon-close"></i></a>
                    <a href="javascript:;" class="action preview" @click="doPreview()"><i class="iconfont icon-eye"></i></a>
                </div>
                <div class="cover ub-cover-1-1" :style="{backgroundImage:`url(${currentData})`}"></div>
            </div>
        </div>
        <div>
            <a href="javascript:;" class="btn" v-if="galleryEnable" @click="doSelect">
                <i class="iconfont icon-category"></i>
                {{ selectText }}
            </a>
            <div class="tw-inline-block tw-align-top" v-if="uploadEnable">
                <UploadButton :url="imageDialogUrl" category="image" auto-save @save="onUploadSuccess"/>
            </div>
        </div>
        <el-dialog class="pb-image-selector-preview" :visible.sync="previewVisible" append-to-body>
            <img width="100%" :src="currentData"/>
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
import {FieldVModel} from "../lib/fields-config";
import UploadButton from "./UploadButton";

export default {
    name: "ImageSelector",
    mixins: [FieldVModel],
    components: {DataSelector, UploadButton},
    props: {
        data: {
            type: String,
            default: ''
        },
        imageDialogUrl: {
            type: String,
            default: '/member_data/file_manager'
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
        selectText: {
            type: String,
            default: '选择图片',
        },
        // uploadDirectMode: {
        //     type: Boolean,
        //     default: false,
        // },
        childKey: {
            type: String,
            default: '_child',
        },
        doSelectCustom: {
            type: Function,
            default: null,
        },
        uploadEnable: {
            type: Boolean,
            default: false,
        },
        galleryEnable: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            previewVisible: false,
        }
    },
    computed: {
        directUploadUrl() {
            return this.imageDialogUrl + '/image'
        }
    },
    methods: {
        doImageSelect(files) {
            this.currentData = files[0].path
        },
        onUploadSuccess(data) {
            this.currentData = data.fullPath
        },
        doDelete() {
            this.currentData = ''
        },
        doPreview() {
            this.previewVisible = true
        },
        doSelect() {
            if (null === this.doSelectCustom) {
                this.$refs.imageDialog.show()
            } else {
                this.doSelectCustom(path => {
                    this.currentData = path
                })
            }
        },
        // handleAvatarSuccess(res, file) {
        //     if (res.code) {
        //         this.$dialog.tipError(res.msg)
        //         return
        //     }
        //     this.currentData = res.data.path
        // },
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
